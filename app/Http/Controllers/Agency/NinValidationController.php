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

            // 10. Create service record and send to api if the service required api
            
            // API Integration (Post method)
            $apiKey = env('NIN_API_KEY');
            $url = 'https://s8v.ng/api/validation';
            $payload = ['nin' => $request->nin, 'error' => $serviceField->field_name, 'api' => $apiKey];

            $agentServiceStatus = 'processing';
            $comment = 'Request submitted, processing...';
            $isSuccess = false;

            try {
                $response = Http::timeout(30)->post($url, $payload);
                $apiResponseData = $response->json();

                if ($response->successful() && isset($apiResponseData['status']) && 
                   ($apiResponseData['status'] == 'success' || $apiResponseData['status'] == 'successful')) {
                    $isSuccess = true;
                    $agentServiceStatus = 'successful';
                    $comment = 'NIN validation is created successful';
                } else {
                     $comment = $apiResponseData['message'] ?? 'API Error';
                }
            } catch (\Exception $e) {
                Log::error('Validation API Error: ' . $e->getMessage());
                $comment = 'Connection Error: Provider unreachable. queued for retry.';
            }

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
                'status' => $agentServiceStatus,
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'comment' => $comment,
                'performed_by' => $performedBy,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isSuccess ? 'Request submitted successfully' : 'Request submitted, pending processing',
                'data' => [
                    'reference' => $agentService->reference,
                    'trx_ref' => $transactionRef,
                    'status' => $agentServiceStatus,
                    'nin' => $request->nin,
                    'response' => $comment
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Validation Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System Error: ' . $e->getMessage()], 500);
        }
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
                    'nin' => 'required|string',
                ]);
                if ($validator->fails()) {
                     return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
                }
                
                $agentService = AgentService::where('nin', $request->nin)
                    ->where('service_type', 'NIN_VALIDATION') 
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
                
            if (!$agentService) {
                    return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
            }

            // Call Upstream Status API
            $apiKey = env('NIN_API_KEY');
            $url = 'https://s8v.ng/api/validation/status';
            $payload = ['nin' => $agentService->nin, 'token' => $apiKey];

            $response = Http::post($url, $payload);
            $apiResponse = $response->json();
            $cleanResponse = $this->cleanApiResponse($apiResponse);

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

            // NO REFUND FOR VALIDATION as per request ("refund attempt on validation since validation have no refund")

            return response()->json([
                'success' => true,
                'nin' => $agentService->nin,
                'status' => $agentService->status,
                'comment' => $cleanResponse,
                'message' => 'Status checked.'
            ]);

        } catch (\Exception $e) {
            Log::error('Validation Status Check Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to check status'], 400);
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

                // Handle Refund logic via IPE Controller method if IPE
                if ($newStatus === 'failed' && strtoupper($submission->service_type) === 'IPE') {
                     // Since we split controllers, we can instantiate IPE controller or just copy logic?
                     // Safer to duplicate simple logic or create shared service. 
                     // For now, I'll embed the IPE refund check here since webhook is shared.
                     $this->processIpeRefund($submission);
                }
            }
        }
        return response()->json(['success' => true, 'message' => 'Webhook received successfully']);
    }

    // --- Helpers ---

    private function processIpeRefund(AgentService $agentService)
    {
        if (strtoupper($agentService->service_type) !== 'IPE') return;
        if ($agentService->is_refunded) return;

         try {
            $user = \App\Models\User::find($agentService->user_id);
            if (!$user) return;

            DB::beginTransaction();
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if ($wallet) {
                $wallet->balance += $agentService->amount;
                $wallet->save();

                Transaction::create([
                    'transaction_ref' => strtoupper(Str::random(12)),
                    'user_id' => $user->id,
                    'performed_by' => 'System (Webhook)', 
                    'amount' => $agentService->amount,
                    'type' => 'refund',
                    'status' => 'completed',
                    'description' => "Refund 100% for rejected IPE service [{$agentService->service_field_name}], Request ID #{$agentService->id}",
                    'metadata' => ['original_request_id' => $agentService->id],
                ]);

                $agentService->update(['is_refunded' => true]); 
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook Refund Error: ' . $e->getMessage());
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
        $s = strtolower(trim((string) $status));
        return match ($s) {
            'successful', 'success', 'resolved', 'approved', 'in_progress', 'completed' => 'successful',
            'processing', 'pending', 'submitted', 'new' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid' => 'failed',
            default => 'pending',
        };
    }
}
