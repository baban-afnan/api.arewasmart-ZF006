<?php

namespace App\Http\Controllers\Billpayment;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
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
        $service = Service::where('name', 'Data')->first();
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
                $field = DB::table('service_fields')
                    ->where('service_id', 16) // Data service ID
                    ->where('field_code', $fieldCode)
                    ->first();

                if ($field) {
                    $role = $user->role ?? 'user';
                    $priceObj = DB::table('service_prices')
                        ->where('service_fields_id', $field->id)
                        ->where('user_type', $role)
                        ->first();
                    
                    $commissions[$networkCode] = $priceObj ? $priceObj->price : $field->base_price;
                } else {
                    $commissions[$networkCode] = 0;
                }
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
            // 1. Authentication
            $user = $this->authenticateApiUser($request);
            if (!$user || $user->status !== 'active') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized or account restricted.'], 401);
            }

            // 2. Validation
            $validator = Validator::make($request->all(), [
                'network'    => ['required', 'string'],
                'mobileno'   => 'required|numeric|digits:11',
                'bundle'     => 'required|string', // variation_code
                'request_id' => 'nullable|string|unique:transactions,transaction_ref'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
            }

            // 3. Setup
            $requestId = $request->request_id ?? RequestIdHelper::generateRequestId();
            $networkData = $this->normalizeNetwork($request->network);
            
            if (!$networkData) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Invalid Network. Allowed: mtn-data, airtel-data, glo-data, etisalat-data.'
                ], 422);
            }

            $networkCode = $networkData['code'];
            $networkName = $networkData['name'];

            // 4. Bundle Details (Validate against database)
            $variation = DB::table('data_variations')
                ->where('variation_code', $request->bundle)
                ->where('service_id', $networkCode)
                ->first();

            if (!$variation) {
                return response()->json([
                    'status' => 'error', 
                    'message' => "The data plan with code '{$request->bundle}' was not found for the selected network in our database."
                ], 422);
            }

            $amount = $variation->variation_amount;
            $description = $variation->name;

            // 5. Service & Commission Lookup
            $serviceData = $this->getServiceAndCommission($networkCode, $networkName, $user);
            if (!$serviceData['success']) {
                return response()->json(['status' => 'error', 'message' => $serviceData['message']], 503);
            }

            // Calculate payable amount (Discount method)
            $discountPercentage = $serviceData['commission'];
            $discountAmount = ($amount * $discountPercentage) / 100;
            $payableAmount = $amount - $discountAmount;

            // 6. Balance Check (Check against Full Amount since we debit full and then credit cashback)
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->balance < $amount) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient wallet balance. You need ₦' . number_format($amount, 2)], 402);
            }

            // 7. Upstream Request
            $response = $this->callUpstreamApi($requestId, $networkCode, $request->bundle, $request->mobileno);
            
            if (!$response['success']) {
                $transactionRef = $this->generateTransactionRef();
                
                // Log failed transaction if we have response data
                if (isset($response['data'])) {
                    $this->logTransaction($user, $transactionRef, $amount, 'refund', "Failed Data Purchase: {$description} - {$request->mobileno}", [
                        'network_code' => $networkCode, 
                        'network_name' => $networkName, 
                        'phone' => $request->mobileno,
                        'bundle' => $request->bundle,
                        'status' => 'failed',
                        'error_message' => $response['message'] ?? 'Transaction Failed',
                        'upstream_response' => $response['data'],
                        'external_ref' => $requestId
                    ], 'failed');
                }

                return response()->json([
                    'status' => 'error', 
                    'message' => $response['message'] ?? 'Data purchase failed.', 
                    'upstream_response' => $response['data'] ?? null
                ], 400); 
            }

            // 8. Transaction Processing
            return DB::transaction(function () use ($user, $wallet, $request, $requestId, $networkCode, $networkName, $response, $amount, $payableAmount, $discountAmount, $discountPercentage, $description) {
                $transactionRef = $this->generateTransactionRef();

                // Debit the FULL amount 
                $wallet->decrement('balance', $amount);
                
                $this->logTransaction($user, $transactionRef, $amount, 'debit', "Data Purchase: {$description} - {$request->mobileno}", [
                    'network_code' => $networkCode, 
                    'network_name' => $networkName, 
                    'phone' => $request->mobileno,
                    'bundle' => $request->bundle,
                    'original_amount' => $amount,
                    'discount' => 0, 
                    'external_ref' => $requestId
                ]);

                // Commission / Cashback Incentive
                if ($discountAmount > 0) {
                    $wallet->increment('available_balance', $discountAmount);
                    
                    $commissionRef = $this->generateTransactionRef();
                    $this->logTransaction($user, $commissionRef, $discountAmount, 'bonus', "Data Cashback ({$discountPercentage}%)", [
                        'related_transaction_ref' => $transactionRef,
                        'external_ref' => $requestId
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data purchase successful',
                    'data' => [
                        'transaction_ref' => $transactionRef,
                        'request_id' => $requestId,
                        'network' => $networkCode,
                        'network_name' => $networkName,
                        'bundle' => $request->bundle,
                        'plan_name' => $description,
                        'amount' => $amount,
                        'paid_amount' => $amount,
                        'phone' => $request->mobileno,
                        'type' => "Data Purchase",
                        'commission_earned' => $discountAmount,
                        'new_balance' => $wallet->balance,
                        'status' => 'completed'
                    ]
                ], 200);
            });

        } catch (\Throwable $e) {
            Log::critical('Data System Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'System Error: An unexpected error occurred.'], 500);
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
        // Hardcoded mapping of user identifiers to database field_code values
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

        // Find the actual service field ID using the field_code
        $field = DB::table('service_fields')
            ->where('service_id', 16) // Data service ID
            ->where('field_code', $fieldCode)
            ->first();

        if (!$field) {
            return ['success' => false, 'message' => 'Service field not found in database for code ' . $fieldCode];
        }

        $price = DB::table('service_prices')
            ->where('service_fields_id', $field->id)
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
