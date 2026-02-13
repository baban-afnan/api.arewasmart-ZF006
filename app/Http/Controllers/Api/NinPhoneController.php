<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Verification;
use App\Models\Wallet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NinPhoneController extends Controller
{
    /**
     * Display the NIN Phone Verification Documentation and Test Form.
     */
    public function index()
    {
        $user = Auth::user();
        
        $service = Service::where('name', 'verification')->first();
        if (!$service) {
            $service = Service::create([
                'name' => 'verification',
                'description' => 'NIN Verification Services',
                'is_active' => true,
            ]);
        }

        $field = ServiceField::where('field_code', '605')->first(); // Used 605 for NIN Phone
        if (!$field) {
            $field = ServiceField::create([
                'service_id' => $service->id,
                'field_name' => 'NIN Phone Verification',
                'field_code' => '605',
                'base_price' => 0.00,
                'is_active' => true,
            ]);
        }

        $role = $user->role ?? 'user';
        $price = $field->getPriceForUserType($role);

        return view('api.nin_phone', compact('user', 'price', 'field'));
    }

    /**
     * Process NIN Phone Verification Request.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // 1. Identify User (Web or API)
            $user = $this->authenticateApiUser($request);

            // Optional: fallback to default auth if session exists (e.g. testing from browser)
            if (!$user && Auth::check()) {
                $user = Auth::user();
            }

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized. Invalid or missing API Token.'
                ], 401);
            }

            // 2. Validation
            $validator = Validator::make($request->all(), [
                'value' => 'required|string', // Phone Number
                'ref' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 400);
            }

            // 3. Identify Service & User
            $field = ServiceField::where('field_code', '605')->first();
            
            if (!$field || !$field->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'NIN Phone Verification service is currently unavailable.'
                ], 503);
            }

            $role = $user->role ?? 'user';
            $price = $field->getPriceForUserType($role);

            // 4. Wallet Check
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->balance < $price) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient wallet balance. Please fund your wallet.',
                    'balance' => $wallet ? $wallet->balance : 0,
                    'required' => $price
                ], 402);
            }

            // 5. Generate Transaction Reference (Used for API request even if record isn't created yet)
            $transactionRef = 'NINP' . date('is') . strtoupper(Str::random(5));
            $performedBy = $user->first_name . ' ' . $user->last_name;

            // 6. External API Call
            $endpoint = "https://raudah.com.ng/api/nin/phone";
            $accessToken = env('RAUDA_API_TOKEN');

            if (!$accessToken) {
                return response()->json([
                    'status' => false,
                    'message' => 'System configuration error: Missing API credentials.'
                ], 500);
            }

            // Use provided configuration for request
            $postdata = [
                'value' => $request->value,
                'ref' => $request->ref ?? $transactionRef,
            ];

            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "Token " . $accessToken,
                ])->timeout(30)->post($endpoint, $postdata);

                $data = $response->json();

                // 7. Check Response - ONLY CHARGE ON 200 OK
                if ($response->status() !== 200 || !($data['status'] ?? false)) {
                    $rawError = $data['message'] ?? ($data['data']['message'] ?? 'API Submission Failed');
                    $errorMsg = is_array($rawError) ? json_encode($rawError) : $rawError;

                    return response()->json([
                        'status' => false,
                        'message' => 'Upstream Provider Error: ' . $errorMsg,
                        'details' => $data
                    ], 400);
                }

                // Extract nested data from response
                $apiData = $data['data']['data'] ?? $data['data'] ?? $data ?? [];

                // 7b. Verify we have valid data before charging
                if (empty($apiData) || (empty($apiData['firstname']) && empty($apiData['firstName']) && empty($apiData['nin']) && empty($apiData['NIN']))) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Verification failed: No identity data found for this number.',
                        'details' => $data
                    ], 400);
                }

                // 8. Process Success and Charge in a DB Transaction
                return DB::transaction(function () use ($wallet, $price, $apiData, $user, $field, $transactionRef, $performedBy, $data, $request) {
                    
                    // Debit Wallet
                    $wallet->decrement('balance', $price);
                    
                    // Create Completed Transaction Record
                    $transaction = Transaction::create([
                        'transaction_ref' => $transactionRef,
                        'user_id' => $user->id,
                        'amount' => $price,
                        'description' => "NIN Phone Verification for {$request->value}",
                        'type' => 'debit',
                        'status' => 'completed',
                        'trans_source' => 'API',
                        'performed_by' => $performedBy,
                        'metadata' => [
                            'request' => $request->all(),
                            'service_field' => $field->field_name,
                            'service_code' => '605'
                        ],
                    ]);

                    // 9. Save to Verification Table
                    Verification::create([
                        'reference' => $transactionRef,
                        'user_id' => $user->id,
                        'service_field_id' => $field->id,
                        'service_id' => $field->service_id,
                        'transaction_id' => $transaction->id,
                        'firstname' => $apiData['firstname'] ?? $apiData['firstName'] ?? '',
                        'middlename' => $apiData['middlename'] ?? $apiData['middleName'] ?? '',
                        'surname' => $apiData['surname'] ?? $apiData['lastName'] ?? '',
                        'gender' => strtolower($apiData['gender'] ?? ''),
                        'birthdate' => $apiData['birthdate'] ?? $apiData['birthDate'] ?? '',
                        'birthstate' => $apiData['birthstate'] ?? $apiData['birthState'] ?? '',
                        'birthlga' => $apiData['birthlga'] ?? $apiData['birthLga'] ?? '',
                        'birthcountry' => $apiData['birthcountry'] ?? $apiData['birthCountry'] ?? '',
                        'maritalstatus' => $apiData['maritalstatus'] ?? $apiData['maritalStatus'] ?? '',
                        'email' => $apiData['email'] ?? '',
                        'telephoneno' => $apiData['telephoneno'] ?? $apiData['phone'] ?? $apiData['phoneNumber'] ?? $request->value,
                        'residence_address' => $apiData['residence_AdressLine1'] ?? $apiData['residence_address'] ?? '',
                        'residence_state' => $apiData['residence_state'] ?? $apiData['residence_State'] ?? '',
                        'residence_lga' => $apiData['residence_lga'] ?? $apiData['residence_Lga'] ?? '',
                        'residence_town' => $apiData['residence_Town'] ?? $apiData['residence_town'] ?? '',
                        'religion' => $apiData['religion'] ?? '',
                        'employmentstatus' => $apiData['emplymentstatus'] ?? $apiData['employmentStatus'] ?? '',
                        'educationallevel' => $apiData['educationallevel'] ?? $apiData['educationalLevel'] ?? '',
                        'profession' => $apiData['profession'] ?? '',
                        'title' => $apiData['title'] ?? '',
                        'idno' => $apiData['nin'] ?? $apiData['NIN'] ?? '',
                        'photo_path' => $apiData['photo'] ?? $apiData['photo_path'] ?? '',
                        'signature_path' => $apiData['signature'] ?? $apiData['signature_path'] ?? '',
                        'trackingId' => $apiData['trackingId'] ?? '',
                        'performed_by' => $performedBy,
                        'submission_date' => Carbon::now(),
                        'status' => 'successful',
                        'amount' => $price,
                    ]);

                    return response()->json([
                        'status' => true,
                        'message' => 'NIN Phone Verification Successful',
                        'api_response' => $data,
                    ], 200);
                });

            } catch (\Exception $e) {
                Log::error('NIN Phone API/Transaction Error: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::error('NIN Phone Critical Error: ' . $e->getMessage());



            
            return response()->json([
                'status' => false,
                'message' => 'System error occurred while processing verification.',
            ], 500);
        }
    }

    /**
     * Authenticate User via Bearer Token manually
     */
    private function authenticateApiUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            $token = $request->input('api_token');
        }
        
        if (!$token) {
            return null;
        }

        return User::where('api_token', $token)->first();
    }
}
