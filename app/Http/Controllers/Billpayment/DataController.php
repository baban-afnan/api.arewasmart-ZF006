<?php

namespace App\Http\Controllers\Billpayment;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DataController extends Controller
{
    /**
     * Show Data API Documentation
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch Service Price/Commission details for Documentation
        $service = Service::firstOrCreate(['name' => 'Data'], ['description' => 'Data Services', 'is_active' => true]);
        $commissions = [];

        $networks = [
            'mtn-data' => 'MTN Data',
            'airtel-data' => 'Airtel Data',
            'glo-data' => 'Glo Data',
            'etisalat-data' => '9mobile Data'
        ];

        $fieldCodeMap = [
            'mtn-data'      => '104',
            'airtel-data'   => '105',
            'glo-data'      => '106',
            'etisalat-data' => '107',
        ];

        foreach ($networks as $networkCode => $name) {
            $fieldCode = $fieldCodeMap[$networkCode] ?? null;
            
            if ($fieldCode) {
                $field = $service->fields()->where('field_code', $fieldCode)->first()
                    ?? $service->fields()->create([
                        'field_name' => $name,
                        'field_code' => $fieldCode,
                        'base_price' => 0,
                        'is_active' => true
                    ]);

                $role = $user->role ?? 'user';
                $priceObj = $field->prices()->where('user_type', $role)->first();
                
                $commissions[$networkCode] = $priceObj ? $priceObj->price : $field->base_price;
            } else {
                $commissions[$networkCode] = 0;
            }
        }

        return view('billpayment.data', [
            'user' => $user,
            'commissions' => $commissions,
            'networks' => $networks
        ]);
    }

    /**
     * Get Data Variations/Plans
     */
    public function getVariations(Request $request)
    {
        try {
            $query = DB::table('data_variations')
                ->select(['service_name', 'service_id', 'variation_code', 'name', 'variation_amount', 'fixedPrice', 'status'])
                ->where('status', 'enabled');

            if ($request->has('network')) {
                $network = $this->normalizeNetwork($request->network);
                if ($network) {
                    $query->where('service_id', $network['code']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Invalid network specified.'], 422);
                }
            } else {
                // Default to our supported 4 networks if not specified
                $query->whereIn('service_id', ['mtn-data', 'airtel-data', 'glo-data', 'etisalat-data']);
            }

            $variations = $query->get();

            return response()->json([
                'status' => 'success',
                'data' => $variations
            ]);
        } catch (\Throwable $e) {
            Log::error('Fetch variations error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Unable to fetch data plans.'], 500);
        }
    }

    /**
     * Handle Data Purchase Configured for API
     */
    public function purchase(Request $request)
    {
        try {
            // 1. Authenticate user
            $user = $this->authenticateApiUser($request);
            if (!$user || $user->status !== 'active') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized or account restricted.'], 401);
            }

            // 2. Validate request
            $validator = Validator::make($request->all(), [
                'network'    => ['required', 'string'],
                'mobileno'   => 'required|numeric|digits:11',
                'bundle'     => 'required|string', // variation_code
                'request_id' => 'nullable|string|unique:transactions,transaction_ref'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            // 3. Check service active & 4. Calculate price
            $networkData = $this->normalizeNetwork($request->network);
            if (!$networkData) {
                return response()->json(['status' => 'error', 'message' => 'Invalid Network.'], 422);
            }

            $variation = DB::table('data_variations')
                ->where('variation_code', $request->bundle)
                ->where('service_id', $networkData['code'])
                ->first();

            if (!$variation || $variation->status !== 'enabled') {
                return response()->json(['status' => 'error', 'message' => 'Data plan not found or disabled.'], 422);
            }

            $serviceData = $this->getServiceAndCommission($networkData['code'], $networkData['name'], $user);
            if (!$serviceData['success']) {
                return response()->json(['status' => 'error', 'message' => $serviceData['message']], 503);
            }

            $amount = $variation->variation_amount;
            $discountPercentage = $serviceData['commission'];
            $discountAmount = ($amount * $discountPercentage) / 100;

            $requestId = $request->request_id ?? RequestIdHelper::generateRequestId();
            $transactionRef = $this->generateTransactionRef();
            $performedBy = $user->first_name . ' ' . $user->last_name;

            DB::beginTransaction();

            try {
                // 5. Lock wallet row
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

                // 6. Check wallet active
                if (!$wallet || $wallet->status !== 'active') {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Wallet inactive.'], 400);
                }

                // 7. Check balance
                if ($wallet->balance < $amount) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Insufficient wallet balance.'], 402);
                }

                // 8. Create transaction (pending or success)
                $transaction = Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'description' => "Data Purchase: {$variation->name} - {$request->mobileno}",
                    'type' => 'debit',
                    'status' => 'completed',
                    'trans_source' => 'api',
                    'performed_by' => $performedBy,
                    'metadata' => [
                        'network_code' => $networkData['code'], 
                        'network_name' => $networkData['name'], 
                        'phone' => $request->mobileno,
                        'bundle' => $request->bundle,
                        'external_ref' => $requestId
                    ]
                ]);

                // 9. Debit wallet
                $wallet->decrement('balance', $amount);

                // Handle Commission (Cashback)
                if ($discountAmount > 0) {
                    $wallet->increment('available_balance', $discountAmount);
                    Transaction::create([
                        'transaction_ref' => $this->generateTransactionRef(),
                        'user_id' => $user->id,
                        'amount' => $discountAmount,
                        'description' => "Data Cashback ({$discountPercentage}%)",
                        'type' => 'bonus',
                        'status' => 'completed',
                        'trans_source' => 'api',
                        'performed_by' => $performedBy,
                        'metadata' => [
                            'related_transaction_ref' => $transactionRef,
                            'external_ref' => $requestId
                        ]
                    ]);
                }

                // 10. Create service record and send to api if the service required api
                $response = $this->callUpstreamApi($requestId, $networkData['code'], $request->bundle, $request->mobileno);
                
                if (!$response['success']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error', 
                        'message' => $response['message'] ?? 'Data purchase failed.', 
                        'upstream_response' => $response['data'] ?? null
                    ], 400); 
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data purchase successful',
                    'data' => [
                        'transaction_ref' => $transactionRef,
                        'request_id' => $requestId,
                        'network' => $networkData['code'],
                        'network_name' => $networkData['name'],
                        'bundle' => $request->bundle,
                        'plan_name' => $variation->name,
                        'amount' => $amount,
                        'phone' => $request->mobileno,
                        'type' => "Data Purchase",
                        'commission_earned' => $discountAmount,
                        'new_balance' => $wallet->balance,
                        'status' => 'completed'
                    ]
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::critical('Data System Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()], 500);
        }
    }


    // --- Private Helpers ---

    private function normalizeNetwork($input)
    {
        $input = strtolower(trim($input));
        $map = [
            'mtn-data' => ['code' => 'mtn-data', 'name' => 'mtn'],
            'mtn' => ['code' => 'mtn-data', 'name' => 'mtn'],
            'airtel-data' => ['code' => 'airtel-data', 'name' => 'airtel'],
            'airtel' => ['code' => 'airtel-data', 'name' => 'airtel'],
            'glo-data' => ['code' => 'glo-data', 'name' => 'glo'],
            'glo' => ['code' => 'glo-data', 'name' => 'glo'],
            'etisalat-data' => ['code' => 'etisalat-data', 'name' => '9mobile'],
            'etisalat' => ['code' => 'etisalat-data', 'name' => '9mobile'],
            '9mobile' => ['code' => 'etisalat-data', 'name' => '9mobile'],
        ];
        return $map[$input] ?? null;
    }

    private function getServiceAndCommission($code, $name, $user)
    {
        // 1. Ensure "Data" Service exists
        $service = Service::firstOrCreate(
            ['name' => 'Data'],
            ['description' => 'Data purchase services', 'is_active' => true]
        );

        // 2. Network to Field Code Mapping
        $fieldCodeMap = [
            'mtn-data'      => '104',
            'airtel-data'   => '105',
            'glo-data'      => '106',
            'etisalat-data' => '107',
        ];

        $fieldCode = $fieldCodeMap[$code] ?? null;

        if (!$fieldCode) {
            return ['success' => false, 'message' => 'Service field configuration not found for ' . $code];
        }

        // 3. Find or Create the Service Field (the specific network)
        $field = $service->fields()->where('field_code', $fieldCode)->first();

        if (!$field) {
            // Auto-create the field if missing to prevent system burial
            $field = $service->fields()->create([
                'field_name'  => $name . ' Data',
                'field_code'  => $fieldCode,
                'base_price'  => 0, // Default 0% commission if not set
                'is_active'   => true,
                'description' => "Automated entry for {$name} Data"
            ]);
        }

        // 4. Lookup Price/Commission for user role
        $price = $field->prices()
            ->where('user_type', $user->role ?? 'user')
            ->first();

        return [
            'success' => true, 
            'commission' => $price ? $price->price : $field->base_price,
            'service_id' => $field->id
        ];
    }

    private function callUpstreamApi($requestId, $serviceId, $variationCode, $phone)
    {
        try {
            $response = Http::withHeaders([
                'api-key' => env('API_KEY'), 'secret-key' => env('SECRET_KEY')
            ])->post(env('MAKE_PAYMENT'), [
                'request_id' => $requestId, 
                'serviceID' => $serviceId,
                'billersCode' => env('BIILER_CODE'),
                'variation_code' => $variationCode,
                'phone' => $phone
            ]);
            
            $data = $response->json();
            $success = $response->successful() && (
                in_array(($data['code'] ?? ''), ['0', '00', '000', '200']) || 
                strtolower($data['status'] ?? '') === 'success' || 
                stripos($data['message'] ?? '', 'success') !== false
            );

            return ['success' => $success, 'data' => $data, 'message' => $data['message'] ?? null];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => null, 'message' => 'Connection error.'];
        }
    }

    private function logTransaction($user, $ref, $amount, $type, $desc, $meta = [], $status = 'completed')
    {
        Transaction::create([
            'transaction_ref' => $ref, 'user_id' => $user->id, 'amount' => $amount,
            'description' => $desc, 'type' => $type, 'status' => $status,
            'trans_source' => 'api', 'performed_by' => $user->first_name . ' ' . $user->last_name,
            'metadata' => $meta
        ]);
    }

    private function authenticateApiUser(Request $request)
    {
        if ($request->user()) return $request->user();
        $token = $request->bearerToken();
        return $token ? User::where('api_token', $token)->first() : null;
    }

    private function generateTransactionRef()
    {
        return date('Ymd') . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
    }
}
