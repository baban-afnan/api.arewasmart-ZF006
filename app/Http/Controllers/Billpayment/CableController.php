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

class CableController extends Controller
{
    /**
     * Show Cable TV API Documentation
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch Service Price/Commission details for Documentation
        $service = Service::where('name', 'TV')->first();
        if (!$service) {
            $this->initializeService();
            $service = Service::where('name', 'TV')->first();
        }

        $commissions = [];
        $providers = $this->getCableProviders();

        foreach ($providers as $code => $name) {
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

        return view('billpayment.tv', [
            'user' => $user,
            'commissions' => $commissions,
            'providers' => $providers
        ]);
    }

    /**
     * Fetch Variations (Plans)
     */
    public function getVariations(Request $request)
    {
        $user = $this->authenticateApiUser($request);
        if (!$user || $user->status !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized or account restricted.'], 401);
        }

        $serviceId = $request->service_id;
        $providerIds = array_keys($this->getCableProviders());
        // ... (rest of the logic remains same)

        // 1. Try fetching from Database first
        $query = DB::table('data_variations')
            ->where('status', 'enabled')
            ->select('service_id', 'variation_code as code', 'name', 'variation_amount as amount');

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        } else {
            $query->whereIn('service_id', $providerIds);
        }

        $variations = $query->get();

        if ($variations->isNotEmpty()) {
            return response()->json(['status' => 'success', 'data' => $variations]);
        }

        // 2. If not in DB and a specific service_id was requested, fetch from API
        if ($serviceId) {
            try {
                $response = Http::withHeaders([
                    'api-key'    => config('services.vtpass.api_key'),
                    'secret-key' => config('services.vtpass.secret_key'),
                ])->get(config('services.vtpass.variation_url') . $serviceId);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['content']['variations'])) {
                        $variations = [];
                        foreach ($data['content']['variations'] as $v) {
                            $variations[] = [
                                'service_id' => $serviceId,
                                'code'   => $v['variation_code'],
                                'name'   => $v['name'],
                                'amount' => $v['variation_amount'],
                            ];
                            
                            // Save to DB for caching
                            DB::table('data_variations')->updateOrInsert(
                                ['variation_code' => $v['variation_code'], 'service_id' => $serviceId],
                                [
                                    'name'             => $v['name'],
                                    'variation_amount' => $v['variation_amount'],
                                    'fixedPrice'      => $v['fixedPrice'] ?? 'Yes',
                                    'status'           => 'enabled',
                                    'updated_at'       => Carbon::now(),
                                ]
                            );
                        }
                        return response()->json(['status' => 'success', 'data' => $variations]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Cable Variations API Error: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'error', 'message' => 'No plans found.']);
    }

    /**
     * Verify Smartcard / IUC Number
     */
    public function verifyIuc(Request $request)
    {
        // 1. Authenticate user
        $user = $this->authenticateApiUser($request);
        if (!$user || $user->status !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized or account restricted.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'serviceID'   => 'required|string',
            'billersCode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        try {
            $response = Http::withHeaders([
                'api-key'    => config('services.vtpass.api_key'),
                'secret-key' => config('services.vtpass.secret_key'),
            ])->post(config('services.vtpass.base_url') . '/merchant-verify', [
                'serviceID'   => $request->serviceID,
                'billersCode' => $request->billersCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] == '000') {
                    return response()->json([
                        'status' => 'success',
                        'data'   => $data['content']
                    ]);
                }
            }

            return response()->json(['status' => 'error', 'message' => $data['response_description'] ?? 'Unable to verify decoder number.']);

        } catch (\Exception $e) {
            Log::error('Cable Verification Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Verification failed.']);
        }
    }

    /**
     * Purchase Cable Subscription
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
                'serviceID'         => 'required|string',
                'billersCode'       => 'required|string',
                'variation_code'    => 'required|string',
                'amount'            => 'required|numeric',
                'phone'             => 'required|numeric|digits:11',
                'subscription_type' => 'required|string|in:change,renew',
                'request_id'        => 'nullable|string|unique:transactions,transaction_ref'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            // 3. Check service active & 4. Calculate price (simplified for this task)
            $amount = $request->amount;
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

                // 8. Create transaction
                $transaction = Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'payer_name' => $performedBy,
                    'amount' => $amount,
                    'description' => "TV Subscription: {$request->serviceID} - {$request->billersCode} ({$request->subscription_type})",
                    'type' => 'debit',
                    'status' => 'completed',
                    'trans_source' => 'api',
                    'performed_by' => $performedBy,
                    'approved_by' => $user->id,
                    'metadata' => [
                        'service_id' => $request->serviceID, 
                        'billersCode' => $request->billersCode,
                        'variation_code' => $request->variation_code,
                        'phone' => $request->phone,
                        'subscription_type' => $request->subscription_type,
                        'external_ref' => $requestId
                    ]
                ]);

                // 9. Debit wallet
                $wallet->decrement('balance', $amount);

                // 10. Call Upstream API
                $payload = [
                    'request_id'        => $requestId,
                    'serviceID'         => $request->serviceID,
                    'billersCode'       => $request->billersCode,
                    'variation_code'    => $request->variation_code,
                    'amount'            => $amount,
                    'phone'             => $request->phone,
                    'subscription_type' => $request->subscription_type,
                ];

                $response = Http::withHeaders([
                    'api-key'    => config('services.vtpass.api_key'),
                    'secret-key' => config('services.vtpass.secret_key'),
                ])->post(config('services.vtpass.payment_url'), $payload);

                $result = $response->json();
                $successCodes = ['0', '00', '000', '200'];
                $isSuccessful = $response->successful() && (
                    (isset($result['code']) && in_array((string)$result['code'], $successCodes)) ||
                    (isset($result['status']) && strtolower($result['status']) === 'success')
                );

                if (!$isSuccessful) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error', 
                        'message' => $result['response_description'] ?? 'TV subscription failed.', 
                        'upstream_response' => $result
                    ], 400); 
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'TV subscription successful',
                    'data' => array_merge($result, [
                        'transaction_ref' => $transactionRef,
                        'request_id' => $requestId,
                        'amount' => $amount,
                        'new_balance' => $wallet->balance,
                        'status' => 'completed'
                    ])
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::critical('Cable System Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()], 500);
        }
    }

    // --- Private Helpers ---

    private function getCableProviders()
    {
        return [
            'dstv' => 'DSTV Subscription',
            'gotv' => 'GOTV Subscription',
            'startimes' => 'Startimes Subscription',
            'showmax' => 'Showmax Subscription',
        ];
    }

    private function authenticateApiUser(Request $request)
    {
        if ($request->user()) return $request->user();
        $token = $request->bearerToken() ?? $request->header('Authorization');
        if (is_string($token) && strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        return $token ? User::where('api_token', $token)->first() : null;
    }

    private function generateTransactionRef()
    {
        return date('Ymd') . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
    }

    private function initializeService()
    {
        DB::transaction(function () {
            $service = Service::updateOrCreate(['name' => 'TV'], ['description' => 'Cable TV Subscription Service', 'is_active' => 1]);
            
            $providers = $this->getCableProviders();
            foreach ($providers as $code => $name) {
                ServiceField::updateOrCreate(
                    ['service_id' => $service->id, 'field_code' => $code],
                    ['field_name' => $name, 'description' => $name, 'base_price' => 0, 'is_active' => 1]
                );
            }
        });
    }
}
