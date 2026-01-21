<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Verification;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\User;
use Carbon\Carbon;

class NinVerificationController extends Controller
{
    /**
     * Display the NIN Verification Documentation
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch Service Price for Documentation
        // We use the service name 'Verification' and field code '610' (NIN Verification)
        $service = Service::where('name', 'Verification')->first();
        $price = 0;
        
        if ($service) {
            $serviceField = $service->fields()
                ->where('field_code', '610') // 610 is the NIN Verification Code based on logic below
                ->first();
                
            if ($serviceField) {
                // Default to 'user' role if not set
                $role = $user->role ?? 'user'; 
                $price = $serviceField->getPriceForUserType($role);
            }
        }

        return view('api.nin', [
            'user' => $user,
            'verificationPrice' => $price
        ]);
    }

    /**
     * Verify NIN via API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Verify NIN via API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        // 1. Authenticate User via Bearer Token
        $user = $this->authenticateApiUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing API Token.'
            ], 401);
        }

        // 1b. Check User Status
        if ($user->status !== 'active') { 
             return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active please contact admin'
            ], 403);
        }

        // 2. Validate Request
        $validator = Validator::make($request->all(), [
            'nin' => 'required|string|size:11|regex:/^[0-9]{11}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'response_code' => '422'
            ], 422);
        }

        // 3. Get Verification Service
        $service = Service::where('name', 'Verification')->first();

        if (!$service || !$service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Verification service is currently unavailable.'
            ], 503);
        }

        // 4. Get NIN Verification ServiceField (Code 610)
        $serviceField = $service->fields()
            ->where('field_code', '610')
            ->first();

        if (!$serviceField || !$serviceField->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIN verification service field (610) is not active.'
            ], 503);
        }

        // 5. Determine service price based on user role
        $role = $user->role ?? 'user';
        $servicePrice = $serviceField->getPriceForUserType($role);

        // 6. Check Wallet Balance
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->status !== 'active') {
             return response()->json([
                'status' => 'error',
                'message' => 'Your wallet is not active.'
            ], 403);
        }

        if ($wallet->balance < $servicePrice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance. Please fund your wallet.'
            ], 402);
        }

        // 7. Initialize Transaction (Pending State)
        $transactionRef = 'niv' . date('is') . strtoupper(Str::random(5));
        $performedBy = $user->first_name . ' ' . $user->last_name;
        
        // Create transaction record immediately to track the attempt
        $transaction = Transaction::create([
            'transaction_ref' => $transactionRef,
            'user_id' => $user->id,
            'amount' => $servicePrice,
            'description' => "NIN Verification (Pending) - {$serviceField->field_name}",
            'type' => 'debit',
            'status' => 'pending',
            'trans_source' => 'API',
            'performed_by' => $performedBy,
            'metadata' => [
                'service' => 'verification',
                'service_field' => $serviceField->field_name,
                'field_code' => $serviceField->field_code,
                'nin' => $request->nin,
                'trans_source' => 'API',
                'initial_status' => 'pending'
            ],
        ]);

        // 8. Process API Request to Upstream
        try {
            // Generate Request Data
            $requestTime = (int) (microtime(true) * 1000);
            $noncestr = noncestrHelper::generateNonceStr();
            
            $payload = [
                'version' => env('API_VERSION', '1.0'),
                'nonceStr' => $noncestr,
                'requestTime' => $requestTime,
                'nin' => $request->nin,
            ];

            // Generate Signature
            $privateKey = config('keys.private2'); 
            if (!$privateKey) {
                throw new \Exception('System Configuration Error: Private Key missing.');
            }
            $signature = signatureHelper::generate_signature($payload, $privateKey);

            // API Configuration
            $url = env('Domain') . '/api/validator-service/open/nin/inquire';
            $upstreamToken = env('BEARER'); 

            $headers = [
                'Accept: application/json',
                'CountryCode: NG',
                "Signature: $signature",
                'Content-Type: application/json',
                "Authorization: Bearer $upstreamToken",
            ];

            // Execute Request
            $response = $this->makeCurlRequest($url, $payload, $headers);
            $decodedData = json_decode($response, true);

            // Check Upstream Response
            $respCode = $decodedData['respCode'] ?? null;

            if ($respCode === '00000000') {
                
                // --- SUCCESS FLOW (Charged) ---
                return DB::transaction(function () use ($transaction, $wallet, $servicePrice, $decodedData, $user, $serviceField, $service, $transactionRef, $performedBy) {
                    $resData = $decodedData['data'] ?? [];

                    // 1. Update Transaction to Success
                    $transaction->update([
                        'status' => 'completed',
                        'description' => "NIN Verification (API) - {$serviceField->field_name}",
                        'metadata' => array_merge($transaction->metadata ?? [], ['api_response' => 'success', 'upstream_code' => '00000000']),
                    ]);

                    // 2. Debit Wallet
                    $wallet->decrement('balance', $servicePrice);

                    // 3. Create Verification Record
                    $verification = Verification::create([
                        'user_id' => $user->id,
                        'service_field_id' => $serviceField->id,
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'reference' => $transactionRef,
                        'idno' => $resData['nin'] ?? '',
                        'firstname' => $resData['firstName'] ?? '',
                        'middlename' => $resData['middleName'] ?? '',
                        'surname' => $resData['surname'] ?? $resData['lastName'] ?? '',
                        'birthdate' =>  $resData['birthDate'] ?? $resData['birthday'] ?? '',
                        'gender' => $resData['gender'] ?? '',
                        'telephoneno' => $resData['phoneNumber'] ?? '',
                        'photo_path' => $resData['photo'] ?? '',
                        'performed_by'    => $performedBy,
                        'submission_date' => Carbon::now()
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Successful',
                        'data' => [
                            'nin' => $verification->idno,
                            'firstName' => $verification->firstname,
                            'middleName' => $verification->middlename,
                            'surname' => $verification->surname,
                            'gender' => $verification->gender,
                            'birthDate' => $verification->birthdate,
                            'phoneNumber' => $verification->telephoneno,
                            'photo' => $verification->photo_path,
                            'reference' => $verification->reference,
                            'submission_date' => $verification->submission_date->toDateTimeString(),
                        ],
                        'transaction_ref' => $transactionRef,
                        'charge' => $servicePrice
                    ], 200);
                });

            } elseif ($respCode === '99120010') {
                
                // --- NIN NOT FOUND FLOW (Charged) ---
                return DB::transaction(function () use ($transaction, $wallet, $servicePrice, $decodedData, $serviceField, $transactionRef) {
                    
                    // 1. Update Transaction to Completed (Charged)
                    $transaction->update([
                        'status' => 'completed',
                        'description' => "NIN Verification (Not Found) - {$serviceField->field_name}",
                        'metadata' => array_merge($transaction->metadata ?? [], ['api_response' => 'nin_not_found', 'upstream_code' => '99120010']),
                    ]);

                    // 2. Debit Wallet
                    $wallet->decrement('balance', $servicePrice);

                    // 3. Return Success Status (Transaction Successful) but Message indicates Not Found
                    return response()->json([
                        'status' => 'success',
                        'message' => 'NIN do not exist',
                        'data' => null,
                        'transaction_ref' => $transactionRef,
                        'charge' => $servicePrice
                    ], 200);
                });

            } else {
                // --- FAILURE FLOW (Not Charged) ---
                // Covers 99120012 (Parameter error), 99120013 (System Error), and others
                
                $errorMessage = $decodedData['respDescription'] ?? $decodedData['message'] ?? 'Verification failed';
                
                // Specific mapping for known codes if description is missing or generic
                if ($respCode === '99120012') {
                    $errorMessage = 'Parameter error in the interface call.';
                } elseif ($respCode === '99120013') {
                    $errorMessage = 'System Error';
                }

                // Update transaction to failed
                $transaction->update([
                    'status' => 'failed',
                    'description' => 'Failed: ' . $errorMessage,
                    'metadata' => array_merge($transaction->metadata ?? [], ['api_error' => $errorMessage, 'upstream_code' => $respCode ?? 'UNKNOWN']),
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'upstream_code' => $respCode ?? 'UNKNOWN'
                ], 400); // 400 Bad Request
            }

        } catch (\Exception $e) {
            // --- EXCEPTION FLOW ---
            // Update transaction to failed
            if (isset($transaction)) {
                $transaction->update([
                    'status' => 'failed',
                    'description' => 'System Error: ' . $e->getMessage(),
                    'metadata' => array_merge($transaction->metadata ?? [], ['exception' => $e->getMessage()]),
                ]);
            }

             return response()->json([
                'status' => 'error',
                'message' => 'Service Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Authenticate User via Bearer Token manually
     */
    private function authenticateApiUser(Request $request)
    {
        // Check if user is already authenticated via middleware
        if ($request->user()) {
            return $request->user();
        }

        // Check for Bearer token
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }

        // Find user by api_token
        // Assuming 'api_token' column exists on users table as implied by the blade file
        return User::where('api_token', $token)->first();
    }

    /**
     * Helper to make cURL request
     */
    private function makeCurlRequest($url, $data, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('Upstream Connection Error: ' . $error);
        }
        curl_close($ch);

        return $response;
    }
}
