<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Wallet;
use App\Models\Verification;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        $service = Cache::remember('service_verification_bvn', 3600, function() {
            return Service::where('name', 'Verification')->first();
        });

        $price = 0;
        
        if ($service) {
            $serviceField = Cache::remember('service_field_600', 3600, function() use ($service) {
                return $service->fields()
                    ->where('field_code', '600') // BVN Verification Code
                    ->first();
            });
                
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
        $service = Cache::remember('service_verification_bvn', 3600, function() {
            return Service::where('name', 'Verification')->first();
        });

        if (!$service || !$service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Verification service unavailable.'
            ], 503);
        }

        $serviceField = Cache::remember('service_field_600', 3600, function() use ($service) {
            return $service->fields()->where('field_code', '600')->first();
        });

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

        // 5. Prep Transaction details
        $transactionRef = 'B1A' . date('is') . strtoupper(Str::random(5));
        $performedBy = $user->first_name . ' ' . $user->last_name;

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
                 
                 Log::error('BVN API Failed', [
                     'user_id' => $user->id,
                     'status' => $response->status(),
                     'body' => $response->body()
                 ]);

                 return response()->json(['status' => 'error', 'message' => 'Upstream Service Error'], 502);
            }

            $responseData = $response->json();
            $bvnData = $responseData; // Alias
            
            // Log response for debugging
            \Illuminate\Support\Facades\Log::info('BVN API Response', ['data' => $responseData]);

            $respCode = $responseData['respCode'] ?? '00000000';
            
            if ($respCode === '00000000') {
                
                // --- SUCCESS FLOW (Charged) ---
                return DB::transaction(function () use ($servicePrice, $responseData, $user, $serviceField, $service, $transactionRef, $performedBy) {
                    
                    // 1. Lock wallet for final check and decrement
                    $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

                    if (!$wallet || $wallet->balance < $servicePrice) {
                        throw new \Exception('Insufficient balance at time of charge.');
                    }

                    // 2. Create Transaction Record (Completed)
                    $transaction = Transaction::create([
                        'transaction_ref' => $transactionRef,
                        'user_id' => $user->id,
                        'amount' => $servicePrice,
                        'description' => "BVN Verification - {$serviceField->field_name}",
                        'type' => 'debit',
                        'status' => 'completed',
                        'performed_by'    => $performedBy,
                        'trans_source' => 'API',
                        'metadata' => [
                            'service' => 'verification',
                            'service_field' => $serviceField->field_name,
                            'field_code' => $serviceField->field_code,
                            'bvn' => $responseData['data']['bvn'] ?? '',
                            'api_response' => 'success',
                            'upstream_code' => '00000000'
                        ],
                    ]);

                    // 3. Deduct wallet balance
                    $wallet->decrement('balance', $servicePrice);

                    // 4. Create Verification Record
                    Verification::create([
                        'user_id' => $user->id,
                        'service_field_id' => $serviceField->id,
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'reference' => $transactionRef,
                        'idno' => $responseData['data']['bvn'] ?? '',
                        'firstname' => $responseData['data']['firstName'] ?? '',
                        'middlename' => $responseData['data']['middleName'] ?? '',
                        'surname' => $responseData['data']['lastName'] ?? '',
                        'birthdate' =>  $responseData['data']['birthday'] ?? '',
                        'gender' => $responseData['data']['gender'] ?? '',
                        'telephoneno' => $responseData['data']['phoneNumber'] ?? '',
                        'photo_path' => $responseData['data']['photo'] ?? '',
                        'performed_by'    => $performedBy,
                        'submission_date' => Carbon::now()
                    ]);
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'BVN verification successful',
                        'data' => $responseData['data'],
                        'meta' => [
                            'transaction_ref' => $transactionRef,
                            'charge' => $servicePrice,
                            'timestamp' => Carbon::now()->toDateTimeString()
                        ]
                    ], 200);
                });

            } else {
                 // --- NON-CHARGEABLE FLOW (Not Found / Error) ---
                 $errorMessage = $responseData['respDescription'] ?? ($responseData['message'] ?? 'Verification Failed');

                 // Backward compatibility for common not-found codes
                 $status = ($respCode === '99120020') ? 'success' : 'error';
                 $httpCode = ($respCode === '99120020') ? 200 : 422;

                 return response()->json([
                    'status' => $status,
                    'message' => $errorMessage,
                    'response_code' => $respCode,
                    'meta' => [
                        'charge' => 0,
                        'transaction_ref' => $transactionRef
                    ]
                ], $httpCode);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'System Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
