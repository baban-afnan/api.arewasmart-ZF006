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

class NinDemoController extends Controller
{
    /**
     * Display the NIN Demo Documentation and Test Form.
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

        $field = ServiceField::where('field_code', '604')->first();
        if (!$field) {
            $field = ServiceField::create([
                'service_id' => $service->id,
                'field_name' => 'NIN Demo Verification',
                'field_code' => '604',
                'base_price' => 0.00,
                'is_active' => true,
            ]);
        }

        $role = $user->role ?? 'user';
        $price = $field->getPriceForUserType($role);

        return view('api.nin_demo', compact('user', 'price', 'field'));
    }

    /**
     * Process NIN Demo Verification Request.
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
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'gender' => 'required|string|in:M,F,m,f', // Made generic to handle case
                'dateOfBirth' => 'required|string', // format: 22-02-2002
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
            $field = ServiceField::where('field_code', '604')->first();
            
            if (!$field || !$field->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'NIN Demo service is currently unavailable.'
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

            // 5. Initialize Transaction
            $transactionRef = 'NIND' . date('is') . strtoupper(Str::random(5));
            $performedBy = $user->first_name . ' ' . $user->last_name;

            // Start Database Transaction
            DB::beginTransaction();
            
            try {
                $transaction = Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'amount' => $price,
                    'description' => "NIN Demo Verification for {$request->firstName} {$request->lastName}",
                    'type' => 'debit',
                    'status' => 'pending',
                    'trans_source' => 'API', // Changed to API since this is an API controller
                    'performed_by' => $performedBy,
                    'metadata' => [
                        'request' => $request->all(),
                        'service_field' => $field->field_name,
                        'service_code' => '604'
                    ],
                ]);

                // 6. External API Call (RAUDA API)
                $endpoint = env('RAUDA_API_POST');
                $accessToken = env('RAUDA_API_TOKEN');

                if (!$endpoint || !$accessToken) {
                    throw new \Exception('System configuration error: Missing API credentials.');
                }

                $payload = [
                    'category' => 'NIN',
                    'planId' => '3',
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'gender' => strtoupper($request->gender),
                    'dateOfBirth' => $request->dateOfBirth,
                    'ref' => $request->ref ?? $transactionRef,
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "Token " . $accessToken,
                ])->timeout(30)->post($endpoint, $payload); // Added timeout

                $data = $response->json();

                // 7. Check Response
                // Note: Logic allows for handling "fail" status from API differently than "network error"
                if (!$response->successful() || !($data['status'] ?? false)) {
                    $transaction->update(['status' => 'failed']);
                    DB::commit(); 
                    
                    $rawError = $data['message'] ?? ($data['data']['message'] ?? 'API Submission Failed');
                    $errorMsg = is_array($rawError) ? json_encode($rawError) : $rawError;

                    return response()->json([
                        'status' => false,
                        'message' => 'Upstream Provider Error: ' . $errorMsg,
                        'details' => $data
                    ], 400);
                }

                // 8. Process Success
                $wallet->decrement('balance', $price);
                $transaction->update(['status' => 'completed']);

                // Extract nested data from response - Handle multiple nesting levels
                $apiData = $data['data']['data'] ?? $data['data'] ?? $data ?? [];

                // 9. Save to Verification Table
                $verification = Verification::create([
                    'reference' => $transactionRef,
                    'user_id' => $user->id,
                    'service_field_id' => $field->id,
                    'service_id' => $field->service_id,
                    'transaction_id' => $transaction->id,
                    'firstname' => $apiData['firstname'] ?? $apiData['firstName'] ?? $request->firstName,
                    'middlename' => $apiData['middlename'] ?? $apiData['middleName'] ?? '',
                    'surname' => $apiData['surname'] ?? $apiData['lastName'] ?? $request->lastName,
                    'gender' => strtolower($apiData['gender'] ?? $request->gender),
                    'birthdate' => $apiData['birthdate'] ?? $apiData['birthDate'] ?? $request->dateOfBirth,
                    'birthstate' => $apiData['birthstate'] ?? $apiData['birthState'] ?? '',
                    'birthlga' => $apiData['birthlga'] ?? $apiData['birthLga'] ?? '',
                    'birthcountry' => $apiData['birthcountry'] ?? $apiData['birthCountry'] ?? '',
                    'maritalstatus' => $apiData['maritalstatus'] ?? $apiData['maritalStatus'] ?? '',
                    'email' => $apiData['email'] ?? '',
                    'telephoneno' => $apiData['telephoneno'] ?? $apiData['phone'] ?? $apiData['phoneNumber'] ?? '',
                    'residence_address' => $apiData['residence_AdressLine1'] ?? $apiData['residence_address'] ?? '',
                    'residence_state' => $apiData['residence_state'] ?? $apiData['residence_State'] ?? '',
                    'residence_lga' => $apiData['residence_lga'] ?? $apiData['residence_Lga'] ?? '',
                    'residence_town' => $apiData['residence_Town'] ?? $apiData['residence_town'] ?? '',
                    'religion' => $apiData['religion'] ?? '',
                    'employmentstatus' => $apiData['emplymentstatus'] ?? $apiData['employmentStatus'] ?? '',
                    'educationallevel' => $apiData['educationallevel'] ?? $apiData['educationalLevel'] ?? '',
                    'profession' => $apiData['profession'] ?? '',
                    'title' => $apiData['title'] ?? '',
                    'nin' => $apiData['nin'] ?? $apiData['NIN'] ?? '',
                    'photo_path' => $apiData['photo'] ?? $apiData['photo_path'] ?? '',
                    'signature_path' => $apiData['signature'] ?? $apiData['signature_path'] ?? '',
                    'trackingId' => $apiData['trackingId'] ?? '',
                    'performed_by' => $performedBy,
                    'submission_date' => Carbon::now(),
                    'status' => 'successful',
                    'amount' => $price,
                ]);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'NIN Verification Successful',
                    'api_response' => $data,
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('NIN Demo Transaction Error: ' . $e->getMessage());
                throw $e; // Re-throw to be caught by outer catch block
            }

        } catch (\Throwable $e) {
            // Outer catch block ensures we ALWAYS return JSON, never HTML error page
            Log::error('NIN Demo Critical Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'System error occurred while processing verification.',
                'debug_message' => config('app.debug') ? $e->getMessage() : null
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
            $token = $request->input('api_token'); // Also check query/body param
        }
        
        if (!$token) {
            return null;
        }

        return User::where('api_token', $token)->first();
    }
}
