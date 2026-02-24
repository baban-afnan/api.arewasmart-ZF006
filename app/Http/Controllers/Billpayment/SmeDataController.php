<?php

namespace App\Http\Controllers\Billpayment;

use App\Http\Controllers\Controller;
use App\Helpers\RequestIdHelper;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\SmeData;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SmeDataController extends Controller
{
    /**
     * Show SME Data API Documentation
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ensure "SME Data" Service exists for pricing
        $service = Service::firstOrCreate(
            ['name' => 'SME Data'], 
            ['description' => 'SME Data Services with dynamic pricing', 'is_active' => true]
        );

        $networks = [
            'mtn-sme' => 'MTN SME',
            'airtel-sme' => 'Airtel SME',
            'glo-sme' => 'Glo SME',
            'mobile-sme' => '9mobile SME'
        ];

        $fieldCodeMap = [
            'mtn-sme'      => 'SME01',
            'airtel-sme'   => 'SME02',
            'glo-sme'      => 'SME03',
            'mobile-sme' => 'SME04',
        ];

        $prices = [];

        foreach ($networks as $networkCode => $name) {
            $fieldCode = $fieldCodeMap[$networkCode] ?? null;
            
            if ($fieldCode) {
                $field = $service->fields()->where('field_code', $fieldCode)->first()
                    ?? $service->fields()->create([
                        'field_name' => $name,
                        'field_code' => $fieldCode,
                        'base_price' => 0,
                        'is_active' => true,
                        'description' => "Automated entry for {$name}"
                    ]);

                $role = $user->role ?? 'user';
                $priceObj = $field->prices()->where('user_type', $role)->first();
                
                $prices[$networkCode] = ($priceObj ? $priceObj->price : 0) + $field->base_price;
            } else {
                $prices[$networkCode] = 0;
            }
        }

        return view('billpayment.sme_data_doc', [
            'user' => $user,
            'prices' => $prices,
            'networks' => $networks
        ]);
    }

    /**
     * Get SME Data Variations/Plans
     * API Endpoint: /api/v1/sme-data/variations
     */
    public function getVariations(Request $request)
    {
        try {
            $user = $this->authenticateApiUser($request);
            $role = $user->role ?? 'user';

            $query = SmeData::where('status', 'enabled');

            if ($request->has('network')) {
                $network = strtoupper($request->network);
                $query->where('network', $network);
            }

            if ($request->has('type')) {
                $query->where('plan_type', $request->type);
            }

            $variations = $query->get()->map(function ($plan) use ($role) {
                // Calculate final price: amount + service_field (fee + markup)
                $plan->amount = $plan->calculatePriceForRole($role);
                return $plan;
            });

            return response()->json([
                'status' => 'success',
                'data' => $variations
            ]);
        } catch (\Throwable $e) {
            Log::error('Fetch SME variations error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Unable to fetch SME data plans.'], 500);
        }
    }

    /**
     * Handle SME Data Purchase
     * API Endpoint: /api/v1/sme-data/purchase
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
                'network'    => ['required', 'string', 'in:MTN,AIRTEL,GLO,9MOBILE'],
                'mobileno'   => 'required|numeric|digits:11',
                'plan_id'    => 'required|string', // data_id in sme_datas
                'request_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            // 3. Check service active & 4. Calculate price
            $plan = SmeData::where('data_id', $request->plan_id)
                ->where('network', strtoupper($request->network))
                ->where('status', 'enabled')
                ->first();

            if (!$plan) {
                return response()->json(['status' => 'error', 'message' => 'Invalid plan or network mismatch.'], 422);
            }

            $totalAmount = $plan->calculatePriceForRole($user->role ?? 'user');
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
                if ($wallet->balance < $totalAmount) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Insufficient wallet balance.'], 402);
                }

                // 8. Create transaction (pending or success)
                $transaction = Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'payer_name' => $performedBy,
                    'amount' => $totalAmount,
                    'description' => "SME Data Purchase: {$plan->size} {$plan->plan_type} - {$request->mobileno}",
                    'type' => 'debit',
                    'status' => 'completed',
                    'trans_source' => 'api',
                    'performed_by' => $performedBy,
                    'metadata' => [
                        'network' => $plan->network,
                        'plan_id' => $plan->data_id,
                        'phone' => $request->mobileno,
                        'request_id' => $requestId
                    ]
                ]);

                // 9. Debit wallet
                $wallet->decrement('balance', $totalAmount);

                // 10. Create service record and send to api if the service required api
                $response = $this->callDataStation($requestId, $plan, $request->mobileno);

                if (!$response['success']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => $response['message'] ?? 'Purchase failed. Please try again later.',
                    ], 400);
                }

                // Update transaction with upstream ref if available
                if (isset($response['data']['id'])) {
                    $metadata = $transaction->metadata;
                    $metadata['upstream_ref'] = $response['data']['id'];
                    $transaction->update(['metadata' => $metadata]);
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'SME Data purchase successful',
                    'data' => [
                        'transaction_ref' => $transactionRef,
                        'request_id' => $requestId,
                        'amount' => $totalAmount,
                        'phone' => $request->mobileno,
                        'plan' => "{$plan->size} {$plan->plan_type}",
                        'status' => 'completed'
                    ]
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::critical('SME Data Purchase Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }




    /**
     * Call DataStation API
     */
    private function callDataStation($requestId, $plan, $mobileNumber)
    {
        try {
            // Using network codes mapping for DataStation if needed, assuming the plan's network is compatible
            // In the sample controller provided by user, they used $request->network directly.
            // DataStation usually needs a specific network integer ID.
            $networkIdMap = [
                'MTN' => '1',
                'AIRTEL' => '4',
                'GLO' => '2',
                '9MOBILE' => '3'
            ];

            $networkId = $networkIdMap[strtoupper($plan->network)] ?? $plan->network;

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . env('AUTH_TOKEN'),
                'Content-Type' => 'application/json',
            ])->post(env('SME_ENDPOINT'), [
                'network' => $networkId,
                'mobile_number' => $mobileNumber,
                'plan' => $plan->data_id,
                'Ported_number' => true,
            ]);

            $data = $response->json();
            $success = $response->successful() && isset($data['Status']) && $data['Status'] == 'successful';

            return ['success' => $success, 'data' => $data, 'message' => $data['msg'] ?? null];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => null, 'message' => 'Connection error: ' . $e->getMessage()];
        }
    }

    private function authenticateApiUser(Request $request)
    {
        if ($request->user()) return $request->user();
        $token = $request->bearerToken() ?? $request->header('Authorization');
        if (strpos($token, 'Bearer ') === 0) $token = substr($token, 7);
        return $token ? User::where('api_token', $token)->first() : null;
    }

    private function generateTransactionRef()
    {
        return date('YmdHis') . mt_rand(100, 999);
    }
}
