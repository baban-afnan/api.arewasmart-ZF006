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
     * Display the NIN Validation/IPE API Documentation.
     * Only accessible to logged-in users.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access API documentation.');
        }

        $role = $user->role ?? 'user';

        $validationService = Service::where('name', 'Validation')->first();
        $ipeService = Service::where('name', 'IPE')->first();

        // Fetch fields
        $validationFields = $validationService ? $validationService->fields : collect();
        $ipeFields = $ipeService ? $ipeService->fields : collect();

        $services = collect();

        // Populate Validation Services
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

        // Populate IPE Services
        foreach ($ipeFields as $field) {
            if (stripos($field->field_code, '002') === false) continue;
            
            $price = method_exists($field, 'getPriceForUserType') 
                ? $field->getPriceForUserType($role) 
                : ($field->prices()->where('user_type', $role)->value('price') ?? $field->base_price);
            
            $services->push((object)[
                'id' => $field->id, 
                'name' => $field->field_name, 
                'code' => $field->field_code, 
                'price' => $price, 
                'type' => 'IPE'
            ]);
        }

        return view('nin.nin_validation', compact('user', 'services'));
    }

    /**
     * Process NIN Validation/IPE Request (API Only).
     */
    public function store(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'field_code' => 'required',
            'nin' => 'nullable|digits:11',
            'tracking_id' => 'nullable|string|min:15',
        ]);

        if ($validator->fails()) {
             return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // 2. Identify Service
        $fieldCode = $request->field_code;
        $serviceField = ServiceField::with('service')->where('field_code', $fieldCode)->first();
        
        if (!$serviceField) {
             return response()->json(['success' => false, 'message' => 'Invalid Service Field Code.'], 400);
        }

        $service = $serviceField->service;
        // Check if main service is active
        if (!$service || !$service->is_active || !$serviceField->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service is not active'
            ], 503);
        }

        $serviceType = $serviceField->service->name == 'Validation' ? 'validation' : 'ipe';

        if ($serviceType == 'validation' && !$request->nin) {
            return response()->json(['success' => false, 'message' => 'NIN is required for Validation'], 400);
        }
        
        if ($serviceType == 'ipe' && !$request->tracking_id) {
            return response()->json(['success' => false, 'message' => 'Tracking ID is required for IPE'], 400);
        }

        // 3. User Authentication
        $user = $this->authenticateUser($request);
        if (!$user) {
             return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        // 3b. Check User Status
        if ($user->status !== 'active') { 
             return response()->json([
                'success' => false,
                'message' => 'Your account is not active please contact admin'
            ], 403);
        }

        // Re-verify Service Active Status (already fetched field above, but checking proper relationship now)
        if (!$serviceField->service->is_active || !$serviceField->is_active) {
             return response()->json(['success' => false, 'message' => 'Service is not active.'], 503);
        }

        // 4. Wallet Check
        $role = $user->role ?? 'user';
        
        $servicePrice = method_exists($serviceField, 'getPriceForUserType') 
            ? $serviceField->getPriceForUserType($role) 
            : ($serviceField->prices()->where('user_type', $role)->value('price') ?? $serviceField->base_price);

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->balance < $servicePrice) {
            return response()->json(['success' => false, 'message' => 'Insufficient wallet balance.'], 400);
        }

        // 5. Create PENDING Transaction
        $performedBy = $user->first_name . ' ' . $user->last_name;
        $prefix = $serviceType == 'validation' ? 'val' : 'ipe';
        $transactionRef = $prefix . date('is') . strtoupper(Str::random(5));

        $transaction = Transaction::create([
            'transaction_ref' => $transactionRef,
            'user_id' => $user->id,
            'amount' => $servicePrice,
            'description' => "NIN Agent service for {$serviceField->field_name}",
            'type' => 'debit',
            'status' => 'pending', 
            'trans_source' => 'API',
            'performed_by' => $performedBy,
            'metadata' => [
                'service' => $serviceField->service->name,
                'service_field' => $serviceField->field_name,
                'field_code' => $serviceField->field_code,
                'nin' => $request->nin,
                'tracking_id' => $request->tracking_id,
            ],
        ]);

        // 6. External API Call
        $apiKey = env('NIN_API_KEY');
        $url = $serviceType == 'validation' ? 'https://s8v.ng/api/validation' : 'https://www.s8v.ng/api/clearance';
        
        $payload = $serviceType == 'validation' 
            ? ['nin' => $request->nin, 'error' => $serviceField->field_name, 'api' => $apiKey]
            : ['tracking_id' => $request->tracking_id, 'token' => $apiKey];

        try {
            $response = Http::post($url, $payload);
            $data = $response->json();

            // API Failure
            if (!$response->successful() && (!isset($data['status']) || ($data['status'] != 'success' && $data['status'] != 'successful'))) {
                 $transaction->update(['status' => 'failed']);
                 return response()->json(['success' => false, 'message' => 'API Submission Failed: ' . ($data['message'] ?? 'Unknown Error')], 400);
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            $transaction->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Connection Error: Unable to reach service provider.'], 400);
        }

        // 7. DB Transaction (Commit Charge)
        DB::beginTransaction();

        try {
            $wallet->decrement('balance', $servicePrice);
            $transaction->update(['status' => 'completed']);

            $cleanResponse = $this->cleanApiResponse($data);
            $status = $this->normalizeStatus($data['status'] ?? 'processing');

            $agentService = AgentService::create([
                'reference' => $transactionRef,
                'user_id' => $user->id,
                'service_id' => $serviceField->service_id,
                'service_field_id' => $serviceField->id,
                'field_code' => $serviceField->field_code,
                'transaction_id' => $transaction->id,
                'service_type' => $serviceType == 'validation' ? 'NIN_VALIDATION' : 'IPE',
                'nin' => $request->nin,
                'tracking_id' => $request->tracking_id,
                'amount' => $servicePrice,
                'status' => $status,
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'comment' => $cleanResponse,
                'performed_by' => $performedBy,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Request submitted successfully',
                'data' => [
                    'reference' => $agentService->reference,
                    'trx_ref' => $transactionRef,
                    'status' => $status,
                    'response' => $cleanResponse,
                    'comment' => $cleanResponse
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction Error: ' . $e->getMessage());
            $transaction->update(['status' => 'failed']);
            
            return response()->json(['success' => false, 'message' => 'System Error: Failed to record transaction.'], 400);
        }
    }

    /**
     * Check Status (API Only).
     */
    public function checkStatus(Request $request, $id = null)
    {
        try {
            // Validate & Auth
            $user = $this->authenticateUser($request);
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);

            $agentService = null;

            if ($id) {
                // Ensure transaction belongs to user? Usually desirable.
                $agentService = AgentService::where('id', $id)->first();
            } else {
                $validator = Validator::make($request->all(), [
                    'nin' => 'required|string',
                ]);

                if ($validator->fails()) {
                     return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
                }
                
                $agentService = AgentService::where(function($q) use ($request) {
                        $q->where('nin', $request->nin)->orWhere('tracking_id', $request->nin);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
                
            if (!$agentService) {
                    return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
            }

            // Call Upstream Status API
            $apiKey = env('NIN_API_KEY');
            
            if (strtoupper($agentService->service_type) == 'NIN_VALIDATION' || $agentService->service_type == 'validation') {
                $url = 'https://s8v.ng/api/validation/status';
                $payload = ['nin' => $agentService->nin, 'token' => $apiKey];
            } else {
                $url = 'https://www.s8v.ng/api/clearance/status';
                $payload = ['tracking_id' => $agentService->tracking_id, 'token' => $apiKey];
            }

            $response = Http::post($url, $payload);
            $apiResponse = $response->json();
            $cleanResponse = $this->cleanApiResponse($apiResponse);

            $updateData = ['comment' => $cleanResponse];
            $newStatus = null;
            $statusRaw = null;

            // PARSING LOGIC FOR STATUS
            if (isset($apiResponse['status']) && is_string($apiResponse['status'])) {
                $statusRaw = $apiResponse['status'];
            } elseif (isset($apiResponse['response'])) {
                if (is_array($apiResponse['response']) && isset($apiResponse['response']['status'])) {
                    $statusRaw = $apiResponse['response']['status'];
                } elseif (is_string($apiResponse['response'])) {
                     $statusRaw = $apiResponse['response'];
                }
            }

            if ($statusRaw) {
                $newStatus = $this->normalizeStatus($statusRaw);
                $updateData['status'] = $newStatus;
            }

            $agentService->update($updateData);

            $refundMsg = '';
            // CHECK FOR FAILED STATUS & REFUND
            if ($newStatus === 'failed') {
                $refundResult = $this->processRefund($agentService);
                if ($refundResult === 'already_refunded') {
                    // Specific error pattern as requested
                    return response()->json([
                        'success' => false,
                        'message' => 'this request refund was already process',
                        'data' => [
                            'nin' => $agentService->nin,
                            'status' => $agentService->status,
                            'response' => $apiResponse
                        ]
                    ], 400); 
                } elseif ($refundResult === 'refunded') {
                     $refundMsg = ' Refund has been processed.';
                }
            }

            return response()->json([
                'success' => true,
                'nin' => $agentService->nin,
                'tracking_id' => $agentService->tracking_id,
                'status' => $agentService->status,
                'response' => $apiResponse,
                'comment' => $cleanResponse,
                'message' => 'Status checked.' . $refundMsg
            ]);

        } catch (\Exception $e) {
            Log::error('Status Check Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to check status: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Webhook Handler.
     */
    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::info('NIN Validation Webhook Received', $data);

        $identifier = $data['nin'] ?? $data['tracking_id'] ?? null;

        if ($identifier) {
            $submission = AgentService::where(function($q) use ($identifier) {
                    $q->where('nin', $identifier)->orWhere('tracking_id', $identifier);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            if ($submission) {
                $cleanResponse = $this->cleanApiResponse($data);
                $updateData = ['comment' => $cleanResponse];
                $newStatus = null;
                $statusRaw = null;

                if (isset($data['status']) && is_string($data['status'])) {
                    $statusRaw = $data['status'];
                } elseif (isset($data['response'])) {
                    if (is_array($data['response']) && isset($data['response']['status'])) {
                         $statusRaw = $data['response']['status'];
                    } elseif (is_string($data['response'])) {
                         $statusRaw = $data['response'];
                    }
                }

                if ($statusRaw) {
                    $newStatus = $this->normalizeStatus($statusRaw);
                    $updateData['status'] = $newStatus;
                }

                $submission->update($updateData);

                if ($newStatus === 'failed') {
                    $this->processRefund($submission);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Webhook received successfully']);
    }

    // --- Helpers ---

    private function processRefund(AgentService $agentService)
    {
        // 1. Check if refund already exists (Strict)
        $refundExists = Transaction::where('type', 'refund')
            ->where(function ($q) use ($agentService) {
                $q->where('description', 'LIKE', "%Request ID #{$agentService->id}%")
                  ->orWhere('metadata->original_request_id', $agentService->id)
                  ->orWhere('metadata->refund_for_service_id', $agentService->id);
            })
            ->exists();

        if ($refundExists || $agentService->is_refunded) {
             return 'already_refunded';
        }

        $user = \App\Models\User::find($agentService->user_id);
        if (!$user) return 'error';

        $status = 'error';

        DB::beginTransaction();
        try {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            if ($wallet) {
                // Full Refund Amount
                $refundAmount = $agentService->amount; 
                
                // Update wallet balance
                $wallet->balance += $refundAmount;
                $wallet->save();

                // Create refund transaction
                Transaction::create([
                    'transaction_ref' => strtoupper(Str::random(12)),
                    'user_id' => $user->id,
                    'performed_by' => 'System', 
                    'amount' => $refundAmount,
                    'fee' => 0.00,
                    'net_amount' => $refundAmount,
                    'description' => "Refund 100% for rejected service [{$agentService->service_field_name}], Request ID #{$agentService->id}",
                    'type' => 'refund',
                    'status' => 'completed',
                    'metadata' => [
                        'service_id' => $agentService->service_id,
                        'service_field_id' => $agentService->service_field_id,
                        'field_code' => $agentService->field_code,
                        'field_name' => $agentService->service_field_name,
                        'user_role' => $user->role ?? 'user',
                        'base_price' => $agentService->amount, 
                        'percentage_refunded' => 100,
                        'amount_debited_by_system' => 0,
                        'original_request_id' => $agentService->id,
                        'refund_for_service_id' => $agentService->id, // Compat with old checks
                        'forced_refund' => false,
                    ],
                ]);

                // Optional: Mark agent service as refunded if column exists
                // $agentService->update(['is_refunded' => true]); 

                $status = 'refunded';
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund Error: ' . $e->getMessage());
            $status = 'error';
        }
        
        return $status;
    }

    private function authenticateUser(Request $request)
    {
        $apiToken = $request->header('Authorization');
        if (!$apiToken) {
             $apiToken = $request->input('api_token');
        } else {
             if (str_starts_with($apiToken, 'Bearer ')) {
                 $apiToken = substr($apiToken, 7);
             }
        }

        $user = \App\Models\User::where('api_token', $apiToken)->first();
        // Fallback or explicit null
        return $user; 
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
        $s = strtolower(trim((string) $status));
        return match ($s) {
            'successful', 'success', 'resolved', 'approved', 'completed' => 'successful',
            'processing', 'in_progress', 'in-progress', 'pending', 'submitted', 'new' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid', 'no record' => 'failed',
            default => 'pending',
        };
    }
}
