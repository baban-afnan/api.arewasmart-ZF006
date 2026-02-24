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

class AirtimeController extends Controller
{
    /**
     * Show Airtime API Documentation
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch Service Price/Commission details for Documentation
        $service = Service::where('name', 'Airtime')->first();
        $commissionParams = [];

        // Define Network Mapping for Docs
        $networks = [
            '100' => 'Airtel',
            '101' => 'MTN',
            '102' => 'Glo',
            '103' => '9mobile'
        ];

        if ($service) {
            foreach ($networks as $code => $name) {
                 $field = $service->fields()
                    ->where('field_code', $code)
                    ->first();
                 
                 if ($field) {
                     $role = $user->role ?? 'user';
                     $priceObj = $field->prices()->where('user_type', $role)->first();
                     $commission = $priceObj ? $priceObj->price : ($field->base_price ?? 0);
                     $commissionParams[$name] = $commission;
                 } else {
                     // Fallback check by name if code not set
                     $field = $service->fields()->where('field_name', 'LIKE', "%{$name}%")->first();
                     if ($field) {
                        $role = $user->role ?? 'user';
                        $priceObj = $field->prices()->where('user_type', $role)->first();
                        $commission = $priceObj ? $priceObj->price : ($field->base_price ?? 0);
                        $commissionParams[$name] = $commission;
                     }
                 }
            }
        }

        return view('billpayment.airtime', [
            'user' => $user,
            'commissions' => $commissionParams,
            'networks' => $networks
        ]);
    }

    /**
     * Handle Airtime Purchase Configured for API
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
                'network'   => ['required', 'string'],
                'mobileno'  => 'required|numeric|digits:11',
                'amount'    => 'required|numeric|min:50|max:50000',
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

            $serviceData = $this->getServiceAndCommission($networkData['code'], $networkData['name'], $user);
            if (!$serviceData['success']) {
                return response()->json(['status' => 'error', 'message' => $serviceData['message']], 503);
            }

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
                if ($wallet->balance < $request->amount) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Insufficient wallet balance.'], 402);
                }

                // 8. Create transaction (pending or success)
                $transaction = Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'amount' => $request->amount,
                    'description' => "Airtime Purchase - {$request->mobileno}",
                    'type' => 'debit',
                    'status' => 'completed',
                    'trans_source' => 'api',
                    'performed_by' => $performedBy,
                    'metadata' => [
                        'network_code' => $networkData['code'], 
                        'network_name' => $networkData['name'], 
                        'phone' => $request->mobileno,
                        'external_ref' => $requestId
                    ]
                ]);

                // 9. Debit wallet
                $wallet->decrement('balance', $request->amount);

                // Handle Commission (Cashback)
                $commissionAmount = 0;
                if ($serviceData['commission'] > 0) {
                    $commissionAmount = ($request->amount * $serviceData['commission']) / 100;
                    $wallet->increment('available_balance', $commissionAmount);
                    
                    Transaction::create([
                        'transaction_ref' => $this->generateTransactionRef(),
                        'user_id' => $user->id,
                        'amount' => $commissionAmount,
                        'description' => "Airtime Cashback ({$serviceData['commission']}%)",
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
                $response = $this->callUpstreamApi($requestId, $networkData['name'], $request->amount, $request->mobileno);
                
                if (!$response['success']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error', 
                        'message' => $response['message'] ?? 'Airtime purchase failed.', 
                        'upstream_response' => $response['data']
                    ], 400); 
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Airtime purchase successful',
                    'data' => [
                        'transaction_ref' => $transactionRef,
                        'request_id' => $requestId,
                        'network' => $networkData['code'],
                        'network_name' => $networkData['name'],
                        'amount' => $request->amount,
                        'phone' => $request->mobileno,
                        'type' => "Airtime Recharge",
                        'email' => $user->email,
                        'commission_earned' => $commissionAmount,
                        'new_balance' => $wallet->balance,
                        'status' => 'completed'
                    ]
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::critical('Airtime System Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()], 500);
        }
    }


    // --- Private Helper Methods ---

    private function normalizeNetwork($input)
    {
        $input = strtolower(trim($input));
        $map = [
            '100' => ['code' => '100', 'name' => 'airtel'], 'airtel' => ['code' => '100', 'name' => 'airtel'],
            '101' => ['code' => '101', 'name' => 'mtn'],    'mtn' => ['code' => '101', 'name' => 'mtn'],
            '102' => ['code' => '102', 'name' => 'glo'],    'glo' => ['code' => '102', 'name' => 'glo'],
            '103' => ['code' => '103', 'name' => 'etisalat'], 'etisalat' => ['code' => '103', 'name' => 'etisalat'],
            '9mobile' => ['code' => '103', 'name' => 'etisalat']
        ];
        return $map[$input] ?? null;
    }

    private function getServiceAndCommission($code, $name, $user)
    {
        $service = Service::where('name', 'Airtime')->first();
        if (!$service) return ['success' => false, 'message' => 'Service not configured.'];

        $field = $service->fields()->where('field_code', $code)->first() 
                 ?? $service->fields()->where('field_name', 'LIKE', "%{$name}%")->first();
        
        if (!$field) return ['success' => false, 'message' => 'Service field not found.'];

        $price = $field->prices()->where('user_type', $user->role ?? 'user')->first();
        return [
            'success' => true, 
            'commission' => $price ? $price->price : ($field->base_price ?? 0),
            'service_id' => $field->id
        ];
    }

    private function callUpstreamApi($requestId, $networkName, $amount, $phone)
    {
        try {
            $response = Http::withHeaders([
                'api-key' => env('API_KEY'), 'secret-key' => env('SECRET_KEY')
            ])->post(env('MAKE_PAYMENT'), [
                'request_id' => $requestId, 'serviceID' => $networkName,
                'amount' => $amount, 'phone' => $phone
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

    private function logTransaction($user, $ref, $amount, $type, $desc, $meta = [])
    {
        Transaction::create([
            'transaction_ref' => $ref, 'user_id' => $user->id, 'amount' => $amount,
            'description' => $desc, 'type' => $type, 'status' => 'completed',
            'trans_source' => 'api', 'performed_by' => $user->first_name . ' ' . $user->last_name,
            'metadata' => $meta
        ]);
    }

    /**
     * Authenticate User via Bearer Token manually
     */
    private function authenticateApiUser(Request $request)
    {
        if ($request->user()) {
            return $request->user();
        }

        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }

        return User::where('api_token', $token)->first();
    }

    private function generateTransactionRef()
    {
        // Generate a 15-digit number: "DATE(YmdHis) + RAND" is too long (14+), so we use a different approach or substring
        // Requirement: 15 numbers
        // Format: Ymd (8) + Rand (7)
        return date('Ymd') . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
    }
}
