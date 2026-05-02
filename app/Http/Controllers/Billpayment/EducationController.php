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

class EducationController extends Controller
{
    /**
     * Show Education Pin API Documentation
     */
    public function index()
    {
        $user = Auth::user();

        $service = Service::where('name', 'Education')->first();
        if (!$service) {
            $this->initializeService();
            $service = Service::where('name', 'Education')->first();
        }

        $commissions = [];
        $providers = $this->getEducationProviders();

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

        return view('billpayment.education', [
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
        $role = $user->role ?? 'user';

        // If service_id is missing, we fetch everything
        if (!$serviceId) {
            $allVariations = [];

            // 1. Fetch JAMB & WAEC from Database
            $dbVariations = DB::table('data_variations')
                ->where('status', 'enabled')
                ->whereIn('service_id', ['jamb', 'waec'])
                ->select('service_id', 'variation_code as code', 'name', 'variation_amount as amount')
                ->get();

            foreach ($dbVariations as $v) {
                $allVariations[] = $v;
            }

            // 2. Fetch NECO & NABTEB from Service Fields (Fixed Price)
            $fixedServices = ['neco', 'nabteb'];
            $educationService = Service::where('name', 'Education')->first();

            foreach ($fixedServices as $fs) {
                $field = ServiceField::where('service_id', $educationService->id)->where('field_code', $fs)->first();
                if ($field) {
                    $priceObj = DB::table('service_prices')->where('service_fields_id', $field->id)->where('user_type', $role)->first();
                    $allVariations[] = [
                        'service_id' => $fs,
                        'code' => $fs,
                        'name' => strtoupper($fs) . ' PIN',
                        'amount' => $priceObj ? $priceObj->price : $field->base_price
                    ];
                }
            }

            return response()->json(['status' => 'success', 'data' => $allVariations]);
        }

        if (in_array($serviceId, ['neco', 'nabteb'])) {
            // NECO and NABTEB are fixed price from service_fields
            $service = Service::where('name', 'Education')->first();
            $field = ServiceField::where('service_id', $service->id)->where('field_code', $serviceId)->first();

            if (!$field) {
                return response()->json(['status' => 'error', 'message' => 'Service not configured.']);
            }

            $role = $user->role ?? 'user';
            $priceObj = DB::table('service_prices')->where('service_fields_id', $field->id)->where('user_type', $role)->first();
            $amount = $priceObj ? $priceObj->price : $field->base_price;

            return response()->json([
                'status' => 'success',
                'data' => [
                    [
                        'service_id' => $serviceId,
                        'code' => $serviceId,
                        'name' => strtoupper($serviceId) . ' PIN',
                        'amount' => $amount
                    ]
                ]
            ]);
        }

        // For JAMB and WAEC, try fetching from VTPass or DB
        $query = DB::table('data_variations')
            ->where('status', 'enabled')
            ->where('service_id', $serviceId)
            ->select('service_id', 'variation_code as code', 'name', 'variation_amount as amount');

        $variations = $query->get();

        if ($variations->isNotEmpty()) {
            return response()->json(['status' => 'success', 'data' => $variations]);
        }

        // Fetch from VTPass if not in DB
        if (in_array($serviceId, ['jamb', 'waec'])) {
            try {
                $response = Http::withHeaders([
                    'api-key' => config('services.vtpass.api_key'),
                    'secret-key' => config('services.vtpass.secret_key'),
                ])->get(config('services.vtpass.variation_url') . $serviceId);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['content']['variations'])) {
                        $variations = [];
                        foreach ($data['content']['variations'] as $v) {
                            $variations[] = [
                                'service_id' => $serviceId,
                                'code' => $v['variation_code'],
                                'name' => $v['name'],
                                'amount' => $v['variation_amount'],
                            ];

                            DB::table('data_variations')->updateOrInsert(
                                ['variation_code' => $v['variation_code'], 'service_id' => $serviceId],
                                [
                                    'name' => $v['name'],
                                    'variation_amount' => $v['variation_amount'],
                                    'fixedPrice' => $v['fixedPrice'] ?? 'Yes',
                                    'status' => 'enabled',
                                    'updated_at' => Carbon::now(),
                                ]
                            );
                        }
                        return response()->json(['status' => 'success', 'data' => $variations]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Education Variations API Error: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'error', 'message' => 'No plans found.']);
    }

    /**
     * Verify Profile (Mostly for JAMB)
     */
    public function verifyProfile(Request $request)
    {
        $user = $this->authenticateApiUser($request);
        if (!$user || $user->status !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized or account restricted.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'serviceID' => 'required|string|in:jamb',
            'billersCode' => 'required|string', // Profile ID
            'type' => 'required|string'  // utme or direct-entry
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        try {
            $response = Http::withHeaders([
                'api-key' => config('services.vtpass.api_key'),
                'secret-key' => config('services.vtpass.secret_key'),
            ])->post(config('services.vtpass.base_url') . '/merchant-verify', [
                        'serviceID' => $request->serviceID,
                        'billersCode' => $request->billersCode,
                        'type' => $request->type
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] == '000') {
                    return response()->json([
                        'status' => 'success',
                        'data' => $data['content']
                    ]);
                }
            }

            return response()->json(['status' => 'error', 'message' => $data['response_description'] ?? 'Unable to verify profile ID.']);

        } catch (\Exception $e) {
            Log::error('JAMB Verification Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Verification failed.']);
        }
    }

    /**
     * Purchase Education Pin
     */
    public function purchase(Request $request)
    {
        try {
            $user = $this->authenticateApiUser($request);
            if (!$user || $user->status !== 'active') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized or account restricted.'], 401);
            }

            $validator = Validator::make($request->all(), [
                'serviceID' => 'required|string|in:jamb,waec,neco,nabteb',
                'billersCode' => 'required|string', // Profile ID or Phone
                'variation_code' => 'required|string',
                'amount' => 'required|numeric',
                'phone' => 'required|numeric|digits:11',
                'request_id' => 'nullable|string|unique:transactions,transaction_ref'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            $serviceID = $request->serviceID;
            $requestId = $request->request_id ?? RequestIdHelper::generateRequestId();
            $transactionRef = $this->generateTransactionRef();
            $performedBy = $user->first_name . ' ' . $user->last_name;

            // Calculate Price and Commission
            $pricing = $this->calculatePricing($serviceID, $request->variation_code, $request->amount, $user);
            if (!$pricing['success']) {
                return response()->json(['status' => 'error', 'message' => $pricing['message']], 400);
            }

            $finalAmount = $pricing['final_amount'];
            $discountAmount = $pricing['discount_amount'];

            if (in_array($serviceID, ['neco', 'nabteb'])) {
                // Safeguard: Prevent 0.00 purchase if price not set
                if ($pricing['final_amount'] <= 0) {
                    return response()->json(['status' => 'error', 'message' => 'This service is currently unavailable for purchase (Price not set).'], 400);
                }
            }

            DB::beginTransaction();

            try {
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

                if (!$wallet || $wallet->status !== 'active') {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Wallet inactive.'], 400);
                }

                if ($wallet->balance < $finalAmount) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Insufficient wallet balance.'], 402);
                }

                $transaction = Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'payer_name' => $performedBy,
                    'amount' => $finalAmount,
                    'description' => strtoupper($serviceID) . " PIN Purchase: {$request->variation_code} - {$request->billersCode}",
                    'type' => 'debit',
                    'status' => 'completed',
                    'trans_source' => 'api',
                    'performed_by' => $performedBy,
                    'metadata' => [
                        'service_name' => $this->getEducationProviders()[$serviceID] ?? $serviceID,
                        'service_id' => $serviceID,
                        'billersCode' => $request->billersCode,
                        'variation_code' => $request->variation_code,
                        'phone' => $request->phone,
                        'external_ref' => $requestId
                    ]
                ]);

                $wallet->decrement('balance', $finalAmount);

                // Handle Cashback/Discount if applicable
                if ($discountAmount > 0) {
                    $wallet->increment('available_balance', $discountAmount);
                    Transaction::create([
                        'transaction_ref' => $this->generateTransactionRef(),
                        'user_id' => $user->id,
                        'amount' => $discountAmount,
                        'description' => strtoupper($serviceID) . " Cashback",
                        'type' => 'bonus',
                        'status' => 'completed',
                        'trans_source' => 'api',
                        'performed_by' => $performedBy,
                        'metadata' => ['related_transaction' => $transactionRef]
                    ]);
                }

                // Call Upstream API
                $response = $this->callUpstreamApi($serviceID, $requestId, $request->all(), $pricing);

                if (!$response['success']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => $response['message'] ?? 'Purchase failed.',
                        'upstream_response' => $response['data'] ?? null
                    ], 400);
                }

                // Unified PIN Extraction
                $pinData = $this->extractPin($serviceID, $response['data']);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => strtoupper($serviceID) . ' purchase successful',
                    'data' => array_merge($response['data'], [
                        'pin' => $pinData['pin'] ?? 'See response for details',
                        'serial' => $pinData['serial'] ?? null,
                        'transaction_ref' => $transactionRef,
                        'request_id' => $requestId,
                        'amount' => $finalAmount,
                        'new_balance' => $wallet->balance,
                        'status' => 'completed'
                    ])
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::critical('Education System Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()], 500);
        }
    }

    // --- Private Helpers ---

    private function calculatePricing($serviceID, $variationCode, $requestAmount, $user)
    {
        $service = Service::where('name', 'Education')->first();
        $field = ServiceField::where('service_id', $service->id)->where('field_code', $serviceID)->first();

        if (!$field) {
            return ['success' => false, 'message' => 'Service configuration missing.'];
        }

        $role = $user->role ?? 'user';
        $priceObj = DB::table('service_prices')->where('service_fields_id', $field->id)->where('user_type', $role)->first();
        $commissionOrPrice = $priceObj ? $priceObj->price : $field->base_price;

        if (in_array($serviceID, ['jamb', 'waec'])) {
            // JAMB/WAEC: requestAmount is base, commissionOrPrice is fixed amount discount
            $discount = $commissionOrPrice;
            return [
                'success' => true,
                'final_amount' => $requestAmount,
                'discount_amount' => $discount
            ];
        } else {
            // NECO/NABTEB: full charge from commissionOrPrice
            return [
                'success' => true,
                'final_amount' => $commissionOrPrice,
                'discount_amount' => 0
            ];
        }
    }

    private function callUpstreamApi($serviceID, $requestId, $payload, $pricing)
    {
        if (in_array($serviceID, ['jamb', 'waec'])) {
            return $this->callVTPass($serviceID, $requestId, $payload);
        } else {
            return $this->callDataStation($serviceID, $requestId, $payload, $pricing);
        }
    }

    private function callVTPass($serviceID, $requestId, $payload)
    {
        try {
            $vtPayload = [
                'request_id' => $requestId,
                'serviceID' => $serviceID,
                'billersCode' => $payload['billersCode'],
                'variation_code' => $payload['variation_code'],
                'amount' => $payload['amount'],
                'phone' => $payload['phone'],
            ];

            $response = Http::withHeaders([
                'api-key' => config('services.vtpass.api_key'),
                'secret-key' => config('services.vtpass.secret_key'),
            ])->post(config('services.vtpass.payment_url'), $vtPayload);

            $result = $response->json();
            $success = $response->successful() && (in_array(($result['code'] ?? ''), ['000', '00', '0', '200']));

            return ['success' => $success, 'data' => $result, 'message' => $result['response_description'] ?? null];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'VTPass connection error.'];
        }
    }

    private function callDataStation($serviceID, $requestId, $payload, $pricing)
    {
        try {
            // Map serviceID to Datastation network ID (using names as IDs per user request)
            $networkMap = [
                'neco' => 'NECO',
                'nabteb' => 'NABTEB'
            ];

            $dsPayload = [
                'network' => $networkMap[$serviceID],
                'mobile_number' => $payload['phone'],
                'plan' => strtoupper($payload['variation_code']), // Using name as ID
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . config('services.datastation.token'),
                'Content-Type' => 'application/json',
            ])->post(config('services.datastation.epin_endpoint'), $dsPayload);

            $result = $response->json();
            $success = $response->successful() && isset($result['Status']) && $result['Status'] == 'successful';

            return ['success' => $success, 'data' => $result, 'message' => $result['msg'] ?? null];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Datastation connection error.'];
        }
    }

    /**
     * Unified PIN Extraction
     */
    private function extractPin($serviceID, $data)
    {
        $res = ['pin' => null, 'serial' => null];

        if (in_array($serviceID, ['jamb', 'waec'])) {
            // VTPass handles
            if (isset($data['cards'][0])) {
                $card = $data['cards'][0];
                $res['pin'] = $card['pin'] ?? ($card['Pin'] ?? null);
                $res['serial'] = $card['serial'] ?? ($card['Serial'] ?? null);
            } elseif (isset($data['mainToken'])) {
                $res['pin'] = $data['mainToken'];
            } elseif (isset($data['purchased_code'])) {
                $res['pin'] = $data['purchased_code'];
            }
        } else {
            // Datastation handles
            $res['pin'] = $data['pin'] ?? ($data['pin_code'] ?? null);
            $res['serial'] = $data['serial'] ?? ($data['serial_number'] ?? null);
        }

        return $res;
    }

    private function getEducationProviders()
    {
        return [
            'jamb' => 'JAMB & DE PIN',
            'waec' => 'WAEC PIN',
            'neco' => 'NECO PIN',
            'nabteb' => 'NABTEB PIN',
        ];
    }

    private function authenticateApiUser(Request $request)
    {
        if ($request->user())
            return $request->user();
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
            $service = Service::updateOrCreate(['name' => 'Education'], ['description' => 'Education Pin Services', 'is_active' => 1]);

            $providers = $this->getEducationProviders();
            foreach ($providers as $code => $name) {
                ServiceField::updateOrCreate(
                    ['service_id' => $service->id, 'field_code' => $code],
                    ['field_name' => $name, 'description' => $name, 'base_price' => 0, 'is_active' => 1]
                );
            }
        });
    }
}
