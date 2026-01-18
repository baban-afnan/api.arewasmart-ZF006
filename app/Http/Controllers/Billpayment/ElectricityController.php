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

class ElectricityController extends Controller
{
    /**
     * Show Electricity API Documentation
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch Service Price/Commission details for Documentation
        $service = Service::where('name', 'Electricity')->first();
        if (!$service) {
            $this->initializeService();
            $service = Service::where('name', 'Electricity')->first();
        }

        $commissions = [];
        $companies = $this->getElectricityCompanies();

        foreach ($companies as $code => $name) {
            $field = ServiceField::where('service_id', $service->id)
                ->where('field_code', $code)
                ->first();

            if ($field) {
                $role = $user->role ?? 'user';
                $priceObj = DB::table('service_prices')
                    ->where('service_fields_id', $field->id)
                    ->where('user_type', $role)
                    ->first();
                
                $commissions[$code] = $priceObj ? $priceObj->price : $field->base_price;
            } else {
                $commissions[$code] = 0;
            }
        }

        return view('billpayment.electricity', [
            'user' => $user,
            'commissions' => $commissions,
            'companies' => $companies
        ]);
    }

    /**
     * Get Electricity Variations/Companies
     */
    public function getVariations(Request $request)
    {
        try {
            $serviceIDs = [
                'ikeja-electric', 'eko-electric', 'kano-electric', 'portharcourt-electric',
                'jos-electric', 'ibadan-electric', 'kaduna-electric', 'abuja-electric',
                'enugu-electric', 'benin-electric', 'aba-electric', 'yola-electric'
            ];

            // Populate missing variations
            $companies = $this->getElectricityCompanies();
            foreach ($companies as $code => $name) {
                $vtID = $this->getVTpassServiceID($code);
                if ($vtID) {
                    $prepaidCode = $vtID . '-prepaid';
                    $postpaidCode = $vtID . '-postpaid';

                    // Use updateOrInsert with variation_code as the unique key
                    DB::table('data_variations')->updateOrInsert(
                        ['variation_code' => $prepaidCode],
                        [
                            'service_id' => $vtID,
                            'service_name' => $name, 
                            'name' => $name . ' Prepaid', 
                            'variation_amount' => 0, 
                            'fixedPrice' => 'No', 
                            'status' => 'enabled', 
                            'convinience_fee' => 0,
                            'created_at' => now(), 
                            'updated_at' => now()
                        ]
                    );

                    DB::table('data_variations')->updateOrInsert(
                        ['variation_code' => $postpaidCode],
                        [
                            'service_id' => $vtID,
                            'service_name' => $name, 
                            'name' => $name . ' Postpaid', 
                            'variation_amount' => 0, 
                            'fixedPrice' => 'No', 
                            'status' => 'enabled', 
                            'convinience_fee' => 0,
                            'created_at' => now(), 
                            'updated_at' => now()
                        ]
                    );
                }
            }

            // 3. Fetch and return
            $variations = DB::table('data_variations')
                ->select(['service_name', 'service_id', 'variation_code', 'name', 'variation_amount', 'fixedPrice', 'status'])
                ->where('status', 'enabled')
                ->whereIn('service_id', $serviceIDs)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $variations
            ]);
        } catch (\Throwable $e) {
            Log::error('Fetch electricity variations error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Unable to fetch electricity plans.'], 500);
        }
    }

    /**
     * Verify Meter Number
     */
    public function verifyMeter(Request $request)
    {
        // Infer type from variation_code if type is missing
        if (!$request->has('type') && $request->has('variation_code')) {
            $request->merge(['type' => str_contains($request->variation_code, 'postpaid') ? 'postpaid' : 'prepaid']);
        }

        $validator = Validator::make($request->all(), [
            'serviceID' => 'required|string',
            'billersCode' => 'required|string', // Meter Number
            'type' => 'required|string|in:prepaid,postpaid'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        try {
            $response = Http::withHeaders([
                'api-key' => env('API_KEY'), 'secret-key' => env('SECRET_KEY')
            ])->post('https://sandbox.vtpass.com/api/merchant-verify', [
                'serviceID' => $request->serviceID,
                'billersCode' => $request->billersCode,
                'type' => $request->type
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['content']['Customer_Name'])) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data['content']
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $data['content']['error'] ?? 'Unable to verify meter number.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Connection error.'], 500);
        }
    }

    /**
     * Handle Electricity Purchase
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
                'serviceID'   => 'required|string', // e.g., ikeja-electric
                'billersCode' => 'required|string', // Meter Number
                'variation_code' => 'required|string', // e.g., ikeja-electric-prepaid
                'amount'      => 'required|numeric|min:500',
                'phone'       => 'required|numeric|digits:11',
                'request_id'  => 'nullable|string|unique:transactions,transaction_ref'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
            }

            // 3. Setup
            $requestId = $request->request_id ?? RequestIdHelper::generateRequestId();
            $serviceID = $request->serviceID;
            
            // 4. Service & Commission Lookup
            $serviceData = $this->getServiceAndCommission($serviceID, $user);
            if (!$serviceData['success']) {
                return response()->json(['status' => 'error', 'message' => $serviceData['message']], 503);
            }

            $amount = $request->amount;
            $discountPercentage = $serviceData['commission'];
            $discountAmount = ($amount * $discountPercentage) / 100;

            // 5. Balance Check
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->balance < $amount) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient wallet balance. You need ₦' . number_format($amount, 2)], 402);
            }

            // Normalize variation_code for VTpass (it expects 'prepaid' or 'postpaid')
            $vtVariationCode = str_contains($request->variation_code, 'postpaid') ? 'postpaid' : 'prepaid';

            // 6. Upstream Request
            $response = $this->callUpstreamApi($requestId, $serviceID, $vtVariationCode, $request->billersCode, $amount, $request->phone);
            
            if (!$response['success']) {
                $transactionRef = $this->generateTransactionRef();
                
                if (isset($response['data'])) {
                    $this->logTransaction($user, $transactionRef, $amount, 'refund', "Failed Electricity Payment: {$serviceID} - {$request->billersCode}", [
                        'service_id' => $serviceID, 
                        'meter_number' => $request->billersCode,
                        'status' => 'failed',
                        'error_message' => $response['message'] ?? 'Transaction Failed',
                        'upstream_response' => $response['data'],
                        'external_ref' => $requestId
                    ], 'failed');
                }

                return response()->json([
                    'status' => 'error', 
                    'message' => $response['message'] ?? 'Electricity payment failed.', 
                    'upstream_response' => $response['data'] ?? null
                ], 400); 
            }

            // 7. Transaction Processing
            return DB::transaction(function () use ($user, $wallet, $request, $requestId, $serviceID, $response, $amount, $discountAmount, $discountPercentage) {
                $transactionRef = $this->generateTransactionRef();

                // Debit balance
                $wallet->decrement('balance', $amount);
                
                $this->logTransaction($user, $transactionRef, $amount, 'debit', "Electricity Payment: {$serviceID} - {$request->billersCode}", [
                    'service_id' => $serviceID, 
                    'meter_number' => $request->billersCode,
                    'phone' => $request->phone,
                    'external_ref' => $requestId,
                    'token' => $response['data']['purchased_code'] ?? ($response['data']['mainToken'] ?? null)
                ]);

                // Commission / Cashback
                if ($discountAmount > 0) {
                    $wallet->increment('available_balance', $discountAmount);
                    
                    $commissionRef = $this->generateTransactionRef();
                    $this->logTransaction($user, $commissionRef, $discountAmount, 'bonus', "Electricity Cashback ({$discountPercentage}%)", [
                        'related_transaction_ref' => $transactionRef,
                        'external_ref' => $requestId
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Electricity payment successful',
                    'data' => array_merge($response['data'], [
                        'transaction_ref' => $transactionRef,
                        'request_id' => $requestId,
                        'amount' => $amount,
                        'commission_earned' => $discountAmount,
                        'new_balance' => $wallet->balance,
                        'status' => 'completed'
                    ])
                ], 200);
            });

        } catch (\Throwable $e) {
            Log::critical('Electricity System Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'System Error: An unexpected error occurred.'], 500);
        }
    }

    // --- Private Helpers ---

    private function getElectricityCompanies()
    {
        return [
            '108' => 'Ikeja Electric (IKEDC)',
            '109' => 'Eko Electric (EKEDC)',
            '200' => 'Kano Electric (KEDCO)',
            '201' => 'Port Harcourt Electric (PHED)',
            '202' => 'Jos Electric (JED)',
            '203' => 'Ibadan Electric (IBEDC)',
            '204' => 'Kaduna Electric (KAEDCO)',
            '205' => 'Abuja Electric (AEDC)',
            '206' => 'Enugu Electric (EEDC)',
            '207' => 'Benin Electric (BEDC)',
            '208' => 'Aba Electric (ABA)',
            '209' => 'Yola Electric (YEDC)',
        ];
    }

    private function getVTpassServiceID($fieldCode)
    {
        $map = [
            '108' => 'ikeja-electric',
            '109' => 'eko-electric',
            '200' => 'kano-electric',
            '201' => 'portharcourt-electric',
            '202' => 'jos-electric',
            '203' => 'ibadan-electric',
            '204' => 'kaduna-electric',
            '205' => 'abuja-electric',
            '206' => 'enugu-electric',
            '207' => 'benin-electric',
            '208' => 'aba-electric',
            '209' => 'yola-electric',
        ];
        return $map[$fieldCode] ?? null;
    }

    private function getFieldCodeFromServiceID($serviceID)
    {
        $map = [
            'ikeja-electric' => '108',
            'eko-electric' => '109',
            'kano-electric' => '200',
            'portharcourt-electric' => '201',
            'jos-electric' => '202',
            'ibadan-electric' => '203',
            'kaduna-electric' => '204',
            'abuja-electric' => '205',
            'enugu-electric' => '206',
            'benin-electric' => '207',
            'aba-electric' => '208',
            'yola-electric' => '209',
        ];
        return $map[$serviceID] ?? null;
    }

    private function getServiceAndCommission($serviceID, $user)
    {
        $fieldCode = $this->getFieldCodeFromServiceID($serviceID);

        if (!$fieldCode) {
            return ['success' => false, 'message' => 'Invalid electricity company.'];
        }

        $service = Service::where('name', 'Electricity')->first();
        if (!$service) {
            return ['success' => false, 'message' => 'Electricity service not initialized.'];
        }

        $field = ServiceField::where('service_id', $service->id)
            ->where('field_code', $fieldCode)
            ->first();

        if (!$field) {
            return ['success' => false, 'message' => 'Service field configuration not found for ' . $serviceID];
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

    private function callUpstreamApi($requestId, $serviceID, $variationCode, $billersCode, $amount, $phone)
    {
        try {
            $response = Http::withHeaders([
                'api-key' => env('API_KEY'), 'secret-key' => env('SECRET_KEY')
            ])->post(env('MAKE_PAYMENT'), [
                'request_id' => $requestId, 
                'serviceID' => $serviceID,
                'billersCode' => $billersCode,
                'variation_code' => $variationCode,
                'amount' => $amount,
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

    private function initializeService()
    {
        DB::transaction(function () {
            $service = Service::updateOrCreate(['name' => 'Electricity'], ['description' => 'Electricity Bill Payment Service', 'is_active' => 1]);
            
            $companies = $this->getElectricityCompanies();
            foreach ($companies as $code => $name) {
                $field = ServiceField::updateOrCreate(
                    ['service_id' => $service->id, 'field_code' => $code],
                    ['field_name' => $name, 'description' => $name, 'base_price' => 0, 'is_active' => 1]
                );

             
            }
        });
    }
}
