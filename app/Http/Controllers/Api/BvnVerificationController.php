<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\Verification;
use App\Models\User;
use Carbon\Carbon;

class BvnVerificationController extends Controller
{
    /**
     * Display the BVN Verification Documentation
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Fetch Service Price for Documentation
        $service = Service::where('name', 'Verification')->first();
        $price = 0;
        
        if ($service) {
            $serviceField = $service->fields()
                ->where('field_code', '600') // BVN Verification Code
                ->first();
                
            if ($serviceField) {
                // Default to 'user' role if not set
                $role = $user->role ?? 'user'; 
                $price = $serviceField->getPriceForUserType($role);
            }
        }

        return view('api.bvn', [
            'user' => $user,
            'verificationPrice' => $price
        ]);
    }

    /**
     * Validate BVN and charge user via API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        // 1. Validate Request
        $request->validate([
            'bvn' => 'required|string|size:11|regex:/^[0-9]{11}$/',
        ]);

        // 2. Authenticate User via API Token
        $apiToken = $request->header('Authorization');
        if (!$apiToken) {
             $apiToken = $request->input('api_token');
        } else {
             if (str_starts_with($apiToken, 'Bearer ')) {
                 $apiToken = substr($apiToken, 7);
             }
        }

        $user = User::where('api_token', $apiToken)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid API Token.'
            ], 401);
        }

        // 1b. Check User Status
        if ($user->status !== 'active') { 
             return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active please contact admin'
            ], 403);
        }

        // 3. Get Verification Service & Field
        $service = Service::where('name', 'Verification')->first();
        if (!$service || !$service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Verification service unavailable.'
            ], 503);
        }

        $serviceField = $service->fields()->where('field_code', '600')->first();
        if (!$serviceField || !$serviceField->is_active) {
             return response()->json([
                'status' => 'error',
                'message' => 'BVN verification service unavailable.'
            ], 503);
        }

        // 4. Check Wallet Balance
        $servicePrice = $serviceField->getPriceForUserType($user->role);
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->status !== 'active') {
             return response()->json([
                'status' => 'error',
                'message' => 'Wallet inactive or not found.'
            ], 403);
        }

        if ($wallet->balance < $servicePrice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance.',
                'required' => $servicePrice,
                'balance' => $wallet->balance
            ], 402);
        }

        // 5. Initialize Transaction (Pending State)
        $transactionRef = 'b1vn' . date('is') . strtoupper(Str::random(5));
        $performedBy = $user->first_name . ' ' . $user->last_name;

        // Create transaction record immediately
        $transaction = Transaction::create([
            'transaction_ref' => $transactionRef,
            'user_id' => $user->id,
            'amount' => $servicePrice,
            'description' => "BVN Verification (Pending) - {$serviceField->field_name}",
            'type' => 'debit',
            'status' => 'pending',
            'performed_by'    => $performedBy,
            'trans_source' => 'API',
            'metadata' => [
                'service' => 'verification',
                'service_field' => $serviceField->field_name,
                'field_code' => $serviceField->field_code,
                'bvn' => $request->bvn,
                'user_role' => $user->role,
                'trans_source' => 'API',
                'initial_status' => 'pending'
            ],
        ]);

        try {
            // 6. Call External API
            $requestTime = (int) (microtime(true) * 1000);
            $noncestr = noncestrHelper::generateNonceStr();

            $data = [
                'version' => env('API_VERSION', '1.0'),
                'nonceStr' => $noncestr,
                'requestTime' => $requestTime,
                'bvn' => $request->bvn,
            ];

         
            $privateKey = config('keys.private2') ?? env('PRIVATE_KEY_2'); 
            
            if (!$privateKey) {
                 throw new \Exception('Missing Private Key for BVN Signature');
            }

            $signature = signatureHelper::generate_signature($data, $privateKey);

            $url = env('Domain') . '/api/validator-service/open/bvn/inquire';
            $token = env('BEARER');

            $response = Http::withHeaders([
                'Accept' => 'application/json, text/plain, */*',
                'CountryCode' => 'NG',
                'Signature' => $signature,
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $token",
            ])->post($url, $data);

            if ($response->failed()) {
                 $errorMessage = 'Upstream Service Error: ' . $response->status();
                 
                 // Update transaction to failed
                 $transaction->update([
                    'status' => 'failed',
                    'description' => 'Failed: ' . $errorMessage,
                    'metadata' => array_merge($transaction->metadata ?? [], ['api_error' => $response->body()]),
                 ]);

                 \Illuminate\Support\Facades\Log::error('BVN API Failed', ['body' => $response->body(), 'status' => $response->status()]);
                 return response()->json(['status' => 'error', 'message' => 'Upstream Service Error'], 502);
            }

            $responseData = $response->json();
            $bvnData = $responseData; // Alias
            
            // Log response for debugging
            \Illuminate\Support\Facades\Log::info('BVN API Response', ['data' => $responseData]);

            // 7. Process Response
            // Define chargeable and non-chargeable codes
            $chargeableCodes = [
                '00000000', // Successful
                '99120020', // BVN do not existing
                '99120024', // BVN suspend
                '99120026', // BIRTH_DATE_INVALID
                '99120027', // NAME_INVALID
                '99120028', // GENDER_NULL
                '99120029', // PHOTO_INVALID
            ];
            
            $respCode = $responseData['respCode'] ?? '00000000';
            
            if (in_array($respCode, $chargeableCodes)) {
                
                // --- CHARGEABLE FLOW ---
                $resultResponse = DB::transaction(function () use ($transaction, $wallet, $servicePrice, $bvnData, $user, $serviceField, $service, $transactionRef, $performedBy, $respCode) {
                    
                    $isSuccess = ($respCode == '00000000');
                    $description = $isSuccess ? "BVN Verification - {$serviceField->field_name}" : "BVN Verification (Charged) - Response Code: $respCode";
                    
                    // Update Transaction to Success (Charged)
                    $transaction->update([
                        'status' => 'completed',
                        'description' => $description,
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'price_details' => [
                                'base_price' => $serviceField->base_price,
                                'user_price' => $servicePrice,
                            ],
                            'api_response' => $isSuccess ? 'success' : 'charged_error',
                            'upstream_code' => $respCode
                        ]),
                    ]);

                    // Deduct wallet balance
                    $wallet->decrement('balance', $servicePrice);

                    if ($isSuccess) {
                        // Create Verification Record only on full success
                        Verification::create([
                            'user_id' => $user->id,
                            'service_field_id' => $serviceField->id,
                            'service_id' => $service->id,
                            'transaction_id' => $transaction->id,
                            'reference' => $transactionRef,
                            'idno' => $bvnData['data']['bvn'] ?? '',
                            'firstname' => $bvnData['data']['firstName'] ?? '',
                            'middlename' => $bvnData['data']['middleName'] ?? '',
                            'surname' => $bvnData['data']['lastName'] ?? '',
                            'birthdate' =>  $bvnData['data']['birthday'] ?? '',
                            'gender' => $bvnData['data']['gender'] ?? '',
                            'telephoneno' => $bvnData['data']['phoneNumber'] ?? '',
                            'photo_path' => $bvnData['data']['photo'] ?? '',
                            'performed_by'    => $performedBy,
                            'submission_date' => Carbon::now()
                        ]);
                        
                        return response()->json([
                            'status' => 'success',
                            'message' => 'BVN verification successful',
                            'data' => $bvnData['data'],
                            'meta' => [
                                'transaction_ref' => $transactionRef,
                                'charge' => $servicePrice,
                                'timestamp' => Carbon::now()->toDateTimeString()
                            ]
                        ], 200);
                    } else {
                        // Return charged error response
                        $errorMessage = $bvnData['respDescription'] ?? ($bvnData['message'] ?? 'Verification Failed');
                         return response()->json([
                            'status' => 'success', // Kept as success to indicate charge was made, or could be 'error' but with data. Usually 'success' if charged? User requested "charge" for these errors. 
                            // Note: Usually APIs return error status for logical failures, but if charged, we might want to communicate that. 
                            // Let's stick to 'error' status but with a message indicating charge, OR follow the user's "Successful" annotation for 00000000 only.
                            // The user said "99120020 charge BVN do not existing".
                            // I will return a success=false (error) but with specific code, or maybe the user implies success=true just with error info?
                            // Let's stick to standard HTTP codes: 422 for validation/data errors, but since we CHARGED, maybe 200 is acceptable? 
                            // Actually, let's keep it consistent: failed verification content = error status, but side effect = charged.
                            
                            // Re-reading usage: often if charged, it's treated as a successful *transaction* even if data is 'not found'.
                            // However, let's try to be clear.
                            'status' => 'error',
                            'message' => $errorMessage,
                            'response_code' => $respCode,
                            'meta' => [
                                'charge' => $servicePrice, // Indicate charged
                                'info' => 'Wallet was charged for this request.'
                            ]
                        ], 422);
                    }
                });

                return $resultResponse;

            } else {
                 // --- NON-CHARGEABLE FLOW (System Error / Invalid Params) ---
                 $errorMessage = $responseData['respDescription'] ?? ($responseData['message'] ?? 'Verification Failed');

                 $transaction->update([
                    'status' => 'failed',
                    'description' => 'Failed: ' . $errorMessage,
                    'metadata' => array_merge($transaction->metadata ?? [], ['api_error' => $errorMessage, 'upstream_code' => $respCode]),
                 ]);

                 return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'response_code' => $respCode
                ], 422);
            }

        } catch (\Exception $e) {
            // --- EXCEPTION FLOW ---
            if (isset($transaction)) {
                $transaction->update([
                    'status' => 'failed',
                    'description' => 'System Error: ' . $e->getMessage(),
                    'metadata' => array_merge($transaction->metadata ?? [], ['exception' => $e->getMessage()]),
                ]);
            }
            
            return response()->json([
                'status' => 'error', 
                'message' => 'System Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
