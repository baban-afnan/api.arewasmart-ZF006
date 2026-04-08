<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\AgentService;

class NinIpeController extends Controller
{
    /**
     * Display the IPE API Documentation.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access API documentation.');
        }

        $role = $user->role ?? 'user';
        $ipeService = Service::where('name', 'IPE')->first();
        $ipeFields = $ipeService ? $ipeService->fields : collect();
        $services = collect();

        foreach ($ipeFields as $field) {
            // IPE specific filter if needed, e.g. '002'
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

        return view('nin.ipe', compact('user', 'services'));
    }

    /**
     * Process IPE Request.
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
            'field_code' => 'required|string|max:50',
            'tracking_id' => 'required|string|min:15|max:100|alpha_dash',
        ]);

        if ($validator->fails()) {
             return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

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

        if ($service->name !== 'IPE') {
             return response()->json(['success' => false, 'message' => 'Invalid Service Type for IPE Endpoint.'], 400);
        }

        // 4. Calculate price
        $role = $user->role ?? 'user';
        $servicePrice = method_exists($serviceField, 'getPriceForUserType') 
            ? $serviceField->getPriceForUserType($role) 
            : ($serviceField->prices()->where('user_type', $role)->value('price') ?? $serviceField->base_price);

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
            $transactionRef = 'ipe' . date('is') . strtoupper(Str::random(5));
            
            // 8. Create transaction (pending or success)
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $servicePrice,
                'description' => "NIN Agent service for {$serviceField->field_name} (IPE)",
                'type' => 'debit',
                'status' => 'completed',
                'trans_source' => 'API',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => $serviceField->service->name,
                    'service_field' => $serviceField->field_name,
                    'field_code' => $serviceField->field_code,
                    'tracking_id' => $request->tracking_id,
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
                'service_type' => 'IPE',
                'tracking_id' => $request->tracking_id,
                'amount' => $servicePrice,
                'status' => 'processing',
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'comment' => 'Request submitted, processing...',
                'performed_by' => $performedBy,
            ]);

            // COMMIT EARLY to unlock platform wallet!
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('IPE Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System Error: ' . $e->getMessage()], 500);
        }

        // --- ASYNC SAFE OUTSIDE LOCK ---
        // 11. Interact with API safely without choking Database
        $agentServiceStatus = 'processing';
        $comment = 'Request submitted, processing...';
        $isSuccess = false;

        // Check if record already exists and has a conclusive comment
        $existingRecord = AgentService::where('tracking_id', $request->tracking_id)
            ->where('service_type', 'IPE')
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
            $apiKey = env('NIN_API_KEY');
            $payload = ['tracking_id' => $request->tracking_id, 'token' => $apiKey];
            
            $hasConclusiveStatus = false;
            
            try {
                // First, seamlessly check if the Provider already has this tracking_id's status
                $statusUrl = 'https://www.s8v.ng/api/clearance/status';
                $statusResponse = Http::timeout(20)->post($statusUrl, $payload);
                $apiResponse = $statusResponse->json();
                
                if ($statusResponse->successful() && $apiResponse) {
                    $jsonString = is_array($apiResponse) ? json_encode($apiResponse, JSON_PRETTY_PRINT) : (string) $apiResponse;
                    $cleanResponse = str_replace(['{', '}', '"', "'"], '', $jsonString);
                    $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse);
                    $cleanResponse = trim($cleanResponse);

                    $statusRaw = null;
                    if (isset($apiResponse['status']) && is_string($apiResponse['status'])) {
                        $statusRaw = $apiResponse['status'];
                    } elseif (isset($apiResponse['response'])) {
                        if (is_array($apiResponse['response']) && isset($apiResponse['response']['status'])) {
                            $statusRaw = $apiResponse['response']['status'];
                        } elseif (is_string($apiResponse['response'])) {
                             $statusRaw = $apiResponse['response'];
                        }
                    }

                    // If the upstream API returns conclusive data (not 'not found' or 'invalid')
                    if ($statusRaw && !in_array(strtolower(trim($statusRaw)), ['not found', 'invalid tracking id', 'invalid'])) {
                        $newStatus = $this->normalizeStatus($statusRaw);
                        $isSuccess = in_array($newStatus, ['successful', 'completed']);
                        $agentServiceStatus = $newStatus;
                        $comment = $cleanResponse;
                        $hasConclusiveStatus = true;
                    }
                }
            } catch (\Exception $e) {
                Log::error('IPE Status API Check Error in Store: ' . $e->getMessage());
                // Soft error, safely fallback to Create API
            }

            // If Status API didn't return a definitive existing record, natively create a new one
            if (!$hasConclusiveStatus) {
                // API Integration (Post method)
                $url = 'https://www.s8v.ng/api/clearance';
                try {
                    $response = Http::timeout(30)->post($url, $payload);
                    $apiResponseData = $response->json();

                    if ($response->successful() && isset($apiResponseData['status']) && 
                       ($apiResponseData['status'] == 'success' || $apiResponseData['status'] == 'successful')) {
                        $isSuccess = true;
                        $agentServiceStatus = 'successful';
                        $comment = 'IPE created successful';
                    } else {
                        $comment = $apiResponseData['message'] ?? 'API Error';
                    }
                } catch (\Exception $e) {
                    Log::error('IPE API Error: ' . $e->getMessage());
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
            'message' => $isSuccess ? 'Request submitted successfully' : 'Request submitted, pending processing',
            'data' => [
                'reference' => $agentService->reference,
                'trx_ref' => $transactionRef,
                'status' => $agentServiceStatus,
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
                $agentService = AgentService::where('id', $id)->first();
            } else {
                $validator = Validator::make($request->all(), [
                    'tracking_id' => 'required|string',
                ]);
                if ($validator->fails()) {
                     return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
                }
                
                $agentService = AgentService::where('tracking_id', $request->tracking_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
                
            if (!$agentService) {
                    return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
            }

            // CHECK DATABASE FIRST - if comment exists, return cached response
            if (!empty($agentService->comment) && $agentService->comment !== 'Request submitted, processing...') {
                // Data already exists in DB, return it without calling API
                $refundMsg = '';
                if ($agentService->status === 'failed') {
                    $refundResult = $this->processRefund($agentService);
                    if ($refundResult === 'refunded') {
                        $refundMsg = ' Refund has been processed.';
                    }
                }

                return response()->json([
                    'success' => true,
                    'tracking_id' => $agentService->tracking_id,
                    'status' => $agentService->status,
                    'comment' => $agentService->comment,
                    'message' => 'Status retrieved from database.' . $refundMsg,
                    'cached' => true
                ]);
            }

            // DATA NOT IN DB - Call Upstream Status API
            $apiKey = env('NIN_API_KEY');
            $url = 'https://www.s8v.ng/api/clearance/status';
            $payload = ['tracking_id' => $agentService->tracking_id, 'token' => $apiKey];

            $response = Http::post($url, $payload);
            $apiResponse = $response->json();
            
            // Clean Response logic
            $jsonString = is_array($apiResponse) ? json_encode($apiResponse, JSON_PRETTY_PRINT) : (string) $apiResponse;
            $cleanResponse = str_replace(['{', '}', '"', "'"], '', $jsonString);
            $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse);
            $cleanResponse = trim($cleanResponse);

            $updateData = ['comment' => $cleanResponse];
            $newStatus = null;
            $statusRaw = null;

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
            if ($newStatus === 'failed') {
                $refundResult = $this->processRefund($agentService);
                 if ($refundResult === 'already_refunded') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Status checked. Request was failed/cancelled and already refunded.',
                        'data' => [
                            'tracking_id' => $agentService->tracking_id,
                            'status' => $agentService->status,
                            'response' => $apiResponse
                        ]
                    ], 200); 
                } elseif ($refundResult === 'refunded') {
                     $refundMsg = ' Refund has been processed.';
                }
            }

            return response()->json([
                'success' => true,
                'tracking_id' => $agentService->tracking_id,
                'status' => $agentService->status,
                'response' => $apiResponse,
                'comment' => $cleanResponse,
                'message' => 'Status checked.' . $refundMsg
            ]);

        } catch (\Exception $e) {
            Log::error('IPE Status Check Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to check status'], 400);
        }
    }

    // Reuse helper methods
    private function processRefund(AgentService $agentService)
    {
        // Refund logic for IPE
        if (strtoupper($agentService->service_type) !== 'IPE') return 'not_eligible';

        // Only refund if status is 'failed' (which includes 'cancelled' via mapping)
        if ($agentService->status !== 'failed') return 'not_failed';
        
        $user = \App\Models\User::find($agentService->user_id);
        if (!$user) return 'error';

        $status = 'error';
        DB::beginTransaction();
        try {
            // Acquire Pessimistic Thread Lock globally for user Wallet
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            
            // Execute Database Query specifically sequentially INSIDE Lock guarding race-conditions
            $refundExists = Transaction::where('type', 'refund')
                ->where(function ($q) use ($agentService) {
                    $q->where('description', 'LIKE', "%Request ID #{$agentService->id}%")
                      ->orWhere('metadata->original_request_id', $agentService->id);
                })->exists();

            if ($refundExists) {
                DB::rollBack();
                return 'already_refunded';
            }

            if ($wallet) {
                $wallet->balance += $agentService->amount;
                $wallet->save();

                Transaction::create([
                    'transaction_ref' => strtoupper(Str::random(12)),
                    'user_id' => $user->id,
                    'performed_by' => 'System (Auto)', 
                    'amount' => $agentService->amount,
                    'type' => 'refund',
                    'status' => 'completed',
                    'description' => "Refund 100% for failed/cancelled IPE service [{$agentService->service_field_name}], Request ID #{$agentService->id}",
                    'metadata' => [
                        'original_request_id' => $agentService->id,
                        'original_reference' => $agentService->reference
                    ],
                ]);

                // We skip is_refunded update as column doesn't exist
                $status = 'refunded';
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Refund Error for IPE ID {$agentService->id}: " . $e->getMessage());
            $status = 'error';
        }
        return $status;
    }

    private function authenticateUser(Request $request)
    {
        $apiToken = $request->header('Authorization');
        if (!$apiToken) $apiToken = $request->input('api_token');
        else if (str_starts_with($apiToken, 'Bearer ')) $apiToken = substr($apiToken, 7);
        return \App\Models\User::where('api_token', $apiToken)->first();
    }

    private function normalizeStatus($status): string
    {
        $s = strtolower(trim((string) $status));
        return match ($s) {
            'successful', 'success', 'resolved', 'approved', 'completed' => 'successful',
            'processing', 'in_progress', 'pending', 'submitted', 'new' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid', 'cancelled' => 'failed',
            default => 'pending',
        };
    }
}
