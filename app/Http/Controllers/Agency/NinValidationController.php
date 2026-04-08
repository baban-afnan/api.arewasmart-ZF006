<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\AgentService;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class NinValidationController extends Controller
{
    /**
     * Display the NIN Validation API Documentation.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access API documentation.');
        }

        $role = $user->role ?? 'user';
        $validationService = Service::where('name', 'Validation')->first();
        $validationFields = $validationService ? $validationService->fields : collect();
        $services = collect();

        foreach ($validationFields as $field) {
            if (stripos($field->field_code, '015') === false) continue;
            
            $price = method_exists($field, 'getPriceForUserType') 
                ? $field->getPriceForUserType($role) 
                : ($field->prices()->where('user_type', $role)->value('price') ?? $field->base_price);

            $services->push((object)[
                'id' => $field->id, 
                'name' => $field->field_name, 
                'code' => $field->field_code, 
                'price' => $price, 
                'type' => 'Validation'
            ]);
        }

        return view('nin.nin_validation', compact('user', 'services'));
    }

    /**
     * Process NIN Validation Request.
     */
    public function store(Request $request)
    {
        // 1. Authenticate user
        $user = $this->authenticateUser($request);
        if (!$user) {
             return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        if ($user->status !== 'active') { 
             return response()->json(['success' => false, 'message' => 'Your account is not active.'], 403);
        }

        // 2. Validate request
        $validator = Validator::make($request->all(), [
            'field_code' => 'required',
            'nin' => 'required|digits:11',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
             return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Prevent double click (Race condition)
        $lockKey = "nin_val_lock_{$user->id}_{$request->nin}";
        if (cache()->has($lockKey)) {
            return response()->json(['success' => false, 'message' => 'Please wait a moment before sending the same validation request again.'], 429);
        }
        cache()->put($lockKey, true, 5); // Lock for 5 seconds

        // 3. Check service active
        $fieldCode = $request->field_code;
        $serviceField = ServiceField::with('service')->where('field_code', $fieldCode)->first();
        
        if (!$serviceField) {
             return response()->json(['success' => false, 'message' => 'Invalid Service Field Code.'], 400);
        }

        $service = $serviceField->service;
        if (!$service || !$service->is_active || !$serviceField->is_active) {
            return response()->json(['success' => false, 'message' => 'Service is not active'], 503);
        }

        if ($service->name !== 'Validation') {
             return response()->json(['success' => false, 'message' => 'Invalid Service Type for Validation.'], 400);
        }

        // 4. Calculate price
        $role = $user->role ?? 'user';
        $servicePrice = method_exists($serviceField, 'getPriceForUserType') 
            ? $serviceField->getPriceForUserType($role) 
            : ($serviceField->prices()->where('user_type', $role)->value('price') ?? $serviceField->base_price);

        if ($servicePrice === null) {
            return response()->json(['success' => false, 'message' => 'Service price not configured.'], 400);
        }

        DB::beginTransaction();
        try {
            // 5. Lock wallet row
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            // 6. Check wallet active
            if (!$wallet || $wallet->status !== 'active') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Wallet inactive.'], 400);
            }

            // 7. Check balance
            if ($wallet->balance < $servicePrice) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Insufficient wallet balance.'], 400);
            }

            // Generate Reference
            $performedBy = $user->first_name . ' ' . $user->last_name;
            $transactionRef = 'val' . date('is') . strtoupper(Str::random(5));

            // 8. Create transaction (pending or success)
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $servicePrice,
                'description' => "NIN Agent service for {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'trans_source' => 'API',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => $serviceField->service->name,
                    'service_field' => $serviceField->field_name,
                    'field_code' => $serviceField->field_code,
                    'nin' => $request->nin,
                ],
            ]);

            // 9. Debit wallet
            $wallet->decrement('balance', $servicePrice);

            // 10. Pre-create AgentService record early to release database lock rapidly
            $agentService = AgentService::create([
                'reference' => $transactionRef,
                'user_id' => $user->id,
                'service_id' => $serviceField->service_id,
                'service_field_id' => $serviceField->id,
                'field_code' => $serviceField->field_code,
                'transaction_id' => $transaction->id,
                'service_type' => 'NIN_VALIDATION',
                'nin' => $request->nin,
                'amount' => $servicePrice,
                'status' => 'processing',
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'comment' => 'Request submitted, processing...',
                'performed_by' => $performedBy,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Validation Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System Error: ' . $e->getMessage()], 500);
        }

        // --- ASYNC SAFE OUTSIDE LOCK ---
        // 11. Interact with API safely without choking Database
        $agentServiceStatus = 'processing';
        $comment = 'Request submitted, processing...';
        $isSuccess = false;

        // Check if record already exists and has a conclusive comment
        $existingRecord = AgentService::where('nin', $request->nin)
            ->where('service_type', 'NIN_VALIDATION')
            ->where('id', '!=', $agentService->id) // Skip the exact one we just generated
            ->whereNotNull('comment')
            ->where('comment', '!=', 'Request submitted, processing...')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($existingRecord) {
            // Use stored response
            $isSuccess = in_array($existingRecord->status, ['successful', 'completed']);
            $agentServiceStatus = $existingRecord->status;
            $comment = $existingRecord->comment;
        } else {
            $apiKey = env('IDENFY_API_KEY');
            
            $hasConclusiveStatus = false;
            
            try {
                // First, seamlessly check if the Provider already has this NIN's status
                $statusUrl = 'https://www.idenfy.ng/api/nin-validation-status';
                $statusPayload = ['nin' => $request->nin];

                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->timeout(20)->post($statusUrl, $statusPayload);
                
                $apiResponse = $statusResponse->json();
                
                if ($statusResponse->successful() && $apiResponse) {
                    $jsonString = is_array($apiResponse) ? json_encode($apiResponse, JSON_PRETTY_PRINT) : (string) $apiResponse;
                    $cleanResponse = str_replace(['{', '}', '"', "'"], '', $jsonString);
                    $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse);
                    $cleanResponse = trim($cleanResponse);

                    $statusRaw = null;
                    if (isset($apiResponse['code'])) {
                        $statusRaw = $apiResponse['code'];
                    } elseif (isset($apiResponse['status']) && is_string($apiResponse['status'])) {
                        $statusRaw = $apiResponse['status'];
                    }
                    
                    if ($statusRaw && !in_array(strtolower(trim($statusRaw)), ['not found', 'failed', 'invalid'])) {
                        $newStatus = $this->normalizeStatus($statusRaw);
                        $isSuccess = in_array($newStatus, ['successful', 'completed']);
                        $agentServiceStatus = $newStatus;
                        $comment = $cleanResponse;
                        $hasConclusiveStatus = true;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Validation Status API Check Error in Store: ' . $e->getMessage());
                // Soft error, safely fallback to Create API
            }

            // If Status API didn't return a definitive existing record, natively create a new one
            if (!$hasConclusiveStatus) {
                // API Integration (Post method)
                $url = 'https://www.idenfy.ng/api/nin-validation';
                $payload = [
                    'message' => 'Record not found',
                    'nin' => $request->nin
                ];

                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])->timeout(30)->post($url, $payload);
                    
                    $apiResponseData = $response->json();

                    if ($response->successful() && isset($apiResponseData['status']) && $apiResponseData['status'] === true && isset($apiResponseData['code']) && $apiResponseData['code'] === 'REQUEST_SUBMITTED') {
                        $isSuccess = true;
                        $agentServiceStatus = 'processing';
                        $comment = 'your nin validation request was sent successful know that it make take upto 3 working days';
                    } else {
                         $comment = strip_tags($apiResponseData['message'] ?? 'API Error');
                    }
                } catch (\Exception $e) {
                    Log::error('Validation API Error: ' . $e->getMessage());
                    $comment = 'Connection Error: Provider unreachable. queued for retry.';
                }
            }
        }

        // Apply final conclusions directly onto unlocked row safely.
        $agentService->update([
            'status' => $agentServiceStatus,
            'comment' => $comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => $isSuccess ? 'your nin validation request was sent successful know that it make take upto 3 working days' : 'Request submitted, pending processing',
            'data' => [
                'reference' => $agentService->reference,
                'trx_ref' => $transactionRef,
                'status' => $agentServiceStatus,
                'nin' => $request->nin,
                'response' => $comment
            ]
        ], 200);


    }

    /**
     * Check Status.
     */
    public function checkStatus(Request $request, $id = null)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);

            $agentService = null;

            if ($id) {
                $agentService = AgentService::where('id', $id)
                    ->where('user_id', $user->id)
                    ->first();
            } else {
                $validator = Validator::make($request->all(), [
                    'nin' => 'required|string',
                ]);
                if ($validator->fails()) {
                     return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
                }
                
                $agentService = AgentService::where('nin', $request->nin)
                    ->where('service_type', 'NIN_VALIDATION')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
                
            if (!$agentService) {
                    return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
            }

            // CHECK DATABASE FIRST - if comment exists and is conclusive
            if (!empty($agentService->comment) && $agentService->comment !== 'Request submitted, processing...') {
                return response()->json([
                    'success' => true,
                    'nin' => $agentService->nin,
                    'status' => $agentService->status,
                    'comment' => $agentService->comment,
                    'message' => 'Status retrieved from database.',
                    'cached' => true
                ]);
            }

            // DATA NOT IN DB - Call Upstream Status API
            $apiKey = env('IDENFY_API_KEY');
            $url = 'https://www.idenfy.ng/api/nin-validation-status';
            $payload = ['nin' => $agentService->nin];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, $payload);
            
            $apiResponse = $response->json();
            
            if ($response->successful() && $apiResponse) {
                // Clean Response logic
                $jsonString = is_array($apiResponse) ? json_encode($apiResponse, JSON_PRETTY_PRINT) : (string) $apiResponse;
                $cleanResponse = str_replace(['{', '}', '"', "'"], '', $jsonString);
                $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse);
                $cleanResponse = trim($cleanResponse);

                $updateData = ['comment' => $cleanResponse];
                $newStatus = null;
                $statusRaw = null;

                if (isset($apiResponse['code'])) {
                    $statusRaw = $apiResponse['code'];
                } elseif (isset($apiResponse['status']) && is_string($apiResponse['status'])) {
                    $statusRaw = $apiResponse['status'];
                }

                if ($statusRaw) {
                    $newStatus = $this->normalizeStatus($statusRaw);
                    $updateData['status'] = $newStatus;
                }

                $agentService->update($updateData);

                return response()->json([
                    'success' => true,
                    'nin' => $agentService->nin,
                    'status' => $agentService->status,
                    'response' => $apiResponse,
                    'comment' => $cleanResponse,
                    'message' => 'Status checked.'
                ]);
            }

            return response()->json([
                'success' => true,
                'nin' => $agentService->nin,
                'status' => $agentService->status,
                'comment' => $agentService->comment,
                'message' => 'Status checked.'
            ]);

        } catch (\Exception $e) {
            Log::error('Validation Status Check Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to check status'], 400);
        }
    }

    private function authenticateUser(Request $request)
    {
        $apiToken = $request->header('Authorization');
        if (!$apiToken) $apiToken = $request->input('api_token');
        else if (str_starts_with($apiToken, 'Bearer ')) $apiToken = substr($apiToken, 7);
        return \App\Models\User::where('api_token', $apiToken)->first();
    }

    private function cleanApiResponse($response): string
    {
        $jsonString = is_array($response) ? json_encode($response, JSON_PRETTY_PRINT) : (string) $response;
        $cleanResponse = str_replace(['{', '}', '"', "'"], '', $jsonString);
        $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse);
        return trim($cleanResponse);
    }

    private function normalizeStatus($status): string
    {
        $s = strtoupper(trim((string) $status));
        return match ($s) {
            'SUCCESSFUL', 'SUCCESS' => 'successful',
            'PENDING', 'IN-PROGRESS', 'REQUEST_SUBMITTED', 'PROCESSING' => 'processing',
            'FAILED', 'REJECTED', 'CANCELLED', 'DECLINED' => 'failed',
            default => 'pending',
        };
    }
}
