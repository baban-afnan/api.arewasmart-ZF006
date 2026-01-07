<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

class TinVerificationController extends Controller
{
    /**
     * Display the TIN Registration Documentation
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Fetch Service Price for Documentation
        // Service Name is 'TIN REGISTRATION'
        $service = Service::where('name', 'TIN REGISTRATION')->first();
        $price = 0;

        if ($service) {
            // Try Individual Code 800 first
            $serviceField = $service->fields()
                ->where('field_code', '800') 
                ->first();
            
            // If not found, try Corporate Code 801
            if (!$serviceField) {
                 $serviceField = $service->fields()
                    ->where('field_code', '801') 
                    ->first();
            }

            if ($serviceField) {
                // Default to 'user' role if not set
                $role = $user->role ?? 'user';
                $price = $serviceField->getPriceForUserType($role);
            }
        }

        return view('api.tin', [
            'user' => $user,
            'verificationPrice' => $price
        ]);
    }

    /**
     * Process TIN Registration/Verification via API
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

        // 2. Determine Request Type & Validate
        $type = null;
        $serviceCode = null;

        // User Requirements:
        // Corporate: { type: "2", rc: "8891227" }
        // Individual: { nin: "...", firstName: "...", lastName: "...", dateOfBirth: "..." }

        if ($request->has('rc')) {
            // Corporate Registration
            $type = 'corporate';
            $serviceCode = '801';
            $validator = Validator::make($request->all(), [
                'rc' => 'required|string',
                'type' => 'required|string', // CAC Type
            ]);
        } elseif ($request->has('nin')) {
             // Individual Registration
            $type = 'individual';
            $serviceCode = '800';
            $validator = Validator::make($request->all(), [
                'nin' => 'required|string|size:11|regex:/^[0-9]{11}$/',
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'dateOfBirth' => 'required|string',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request. Provide either "rc" (Corporate) or "nin" (Individual) details.',
                'response_code' => '422'
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'response_code' => '422'
            ], 422);
        }

        // 3. Get TIN REGISTRATION Service
        $service = Service::where('name', 'TIN REGISTRATION')
            ->where('is_active', true)
            ->first();

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'TIN REGISTRATION service is currently unavailable.'
            ], 503);
        }

        // 4. Get Specific ServiceField (800 or 801)
        $serviceField = $service->fields()
            ->where('field_code', $serviceCode)
            ->where('is_active', true)
            ->first();

        if (!$serviceField) {
            return response()->json([
                'status' => 'error',
                'message' => "Service field ($serviceCode) is not configured for TIN REGISTRATION."
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

        // 7. Process API Request to Upstream
        try {
            // Prepare API payload
            $apiKey = 'RTERSwIscARdIERIspENsAnTROcLEgrA'; // Hardcoded as per request
            $url = 'https://live.ninauth.nimc.gov.ng/v1/resolve';
            $payload = [];

            if ($type === 'individual') {
                $payload = [
                    'nin' => $request->nin,
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'dateOfBirth' => $request->dateOfBirth, // Passing directly as per user example
                ];
            } else {
                $payload = [
                    'type' => $request->type,
                    'rc' => $request->rc,
                ];
            }

            // Make API call
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->timeout(30)->post($url, $payload);

            $decodedData = $response->json();

            // Check Upstream Response
            if ($response->successful() && isset($decodedData['success']) && $decodedData['success'] === true) {
                return $this->processChargeAndReturn(
                    $wallet,
                    $servicePrice,
                    $user,
                    $serviceField,
                    $service,
                    $decodedData
                );
            } else {
                 $errorMessage = $decodedData['message'] ?? 'TIN REGISTRATION failed via upstream provider.';
                 return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'upstream_code' => $response->status()
                ], 400);
            }

        } catch (\Exception $e) {
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

        return User::where('api_token', $token)->first();
    }


    /**
     * Process Debit and Record Transaction and Service
     */
    private function processChargeAndReturn($wallet, $servicePrice, $user, $serviceField, $service, $apiData)
    {
        DB::beginTransaction();

        try {
          $transactionRef = 'tin' . date('is') . strtoupper(Str::random(5));
            $performedBy = $user->first_name . ' ' . $user->last_name;
            $resData = $apiData['data'] ?? [];

            // Identify type based on available data
            $isCorporate = isset($resData['rc']) || isset($resData['rc_number']);
            $serviceTypeString = $isCorporate ? 'TIN Corporate' : 'TIN INDIVIDUAL';
            $descriptionType = $isCorporate ? 'CORPORATE' : 'INDIVIDUAL';
            
            // Identify ID number based on type
            // Checking common fields for identification
            $idVerify = $resData['rc'] ?? $resData['nin'] ?? '';

            // 1. Create Transaction
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $servicePrice,
                'description' => "TIN {$descriptionType} (API) - {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'trans_source' => 'API',
                'performed_by'    => $performedBy,
                'metadata' => [
                    'service' => 'tin_registration',
                    'service_type' => $serviceTypeString,
                    'service_field' => $serviceField->field_name,
                    'field_code' => $serviceField->field_code,
                    'id_no' => $idVerify,
                    'trans_source' => 'API'
                ],
            ]);

            // 2. Debit Wallet
            $wallet->decrement('balance', $servicePrice);

            // 3. Create Verification Record (using Verification model per request)
            Verification::create([
                'user_id' => $user->id,
                'service_field_id' => $serviceField->id,
                'service_id' => $service->id,
                'transaction_id' => $transaction->id,
                'reference' => $transactionRef,
                'idno' => $idVerify,
                'firstname' => $resData['firstName'] ?? '',
                'middlename' => $resData['middleName'] ?? '',
                'surname' => $resData['lastName'] ?? '',
                'birthdate' => isset($resData['dateOfBirth']) ? Carbon::createFromFormat('d/m/Y', $resData['dateOfBirth'])->format('Y-m-d') : null,

                'gender' => $resData['gender'] ?? '',
                'telephoneno' => $resData['phoneNumber'] ?? '',
                'photo_path' => $resData['photo'] ?? '',
                
                // New Columns requested
                'tax_id' => $resData['tax_id'] ?? null,
                'tax_residency' => $resData['tax_residency'] ?? null,
                'amount' => $servicePrice, // Saving the charge amount

                'performed_by'    => $performedBy,
                'submission_date' => Carbon::now(),
                'status' => '1' // Assuming '1' effectively means success/completed here as well
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'TIN REGISTRATION Successful',
                'data' => $resData,
                'transaction_ref' => $transactionRef,
                'charge' => $servicePrice
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
