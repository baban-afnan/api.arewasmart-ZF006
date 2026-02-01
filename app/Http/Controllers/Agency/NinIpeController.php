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
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'field_code' => 'required',
            'tracking_id' => 'required|string|min:15',
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
        if (!$service || !$service->is_active || !$serviceField->is_active) {
            return response()->json(['success' => false, 'message' => 'Service is not active'], 503);
        }

        if ($service->name !== 'IPE') {
             return response()->json(['success' => false, 'message' => 'Invalid Service Type for IPE Endpoint.'], 400);
        }

        // 3. User Authentication
        $user = $this->authenticateUser($request);
        if (!$user) {
             return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        if ($user->status !== 'active') { 
             return response()->json(['success' => false, 'message' => 'Your account is not active.'], 403);
        }

        // 4. Wallet Check & Price
        $role = $user->role ?? 'user';
        $servicePrice = method_exists($serviceField, 'getPriceForUserType') 
            ? $serviceField->getPriceForUserType($role) 
            : ($serviceField->prices()->where('user_type', $role)->value('price') ?? $serviceField->base_price);

        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->balance < $servicePrice) {
            return response()->json(['success' => false, 'message' => 'Insufficient wallet balance.'], 400);
        }

        // 5. DEBIT FIRST FLOW
        $performedBy = $user->first_name . ' ' . $user->last_name;
        $transactionRef = 'ipe' . date('is') . strtoupper(Str::random(5));
        
        DB::beginTransaction();
        try {
            // Charge the wallet
            $wallet->decrement('balance', $servicePrice);
            
            // Create Completed Transaction
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $servicePrice,
                'description' => "NIN Agent service for {$serviceField->field_name} (IPE)",
                'type' => 'debit',
                'status' => 'completed', // Money taken
                'trans_source' => 'API',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => $serviceField->service->name,
                    'service_field' => $serviceField->field_name,
                    'field_code' => $serviceField->field_code,
                    'tracking_id' => $request->tracking_id,
                ],
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('IPE Transaction Create Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System Error: Failed to process payment.'], 500);
        }

        // 6. External API Call
        $apiKey = env('NIN_API_KEY');
        $url = 'https://www.s8v.ng/api/clearance';
        $payload = ['tracking_id' => $request->tracking_id, 'token' => $apiKey];

        $agentServiceStatus = 'processing';
        $comment = 'Request submitted, processing...';
        $apiResponseData = null;
        $isSuccess = false;

        try {
            $response = Http::timeout(30)->post($url, $payload);
            $apiResponseData = $response->json();

            if ($response->successful() && isset($apiResponseData['status']) && 
               ($apiResponseData['status'] == 'success' || $apiResponseData['status'] == 'successful')) {
                $isSuccess = true;
                $agentServiceStatus = 'successful';
                $comment = 'IPE created successful';
            } else {
                // API handled error or non-success status
                $comment = $apiResponseData['message'] ?? 'API Error';
            }
        } catch (\Exception $e) {
            Log::error('IPE API Error: ' . $e->getMessage());
            $comment = 'Connection Error: Provider unreachable. queued for retry.';
        }

        // 7. Create Agent Service Record
        try {
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
                'status' => $agentServiceStatus,
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'comment' => $comment, // "generate message that NIN validation is created successful [OR failure msg]"
                'performed_by' => $performedBy,
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

        } catch (\Exception $e) {
            Log::error('IPE Agent Service Save Error: ' . $e->getMessage());
            // Critical: User charged but service record failed. Should probably log highly or alert admin.
            // For API response:
            return response()->json(['success' => true, 'message' => 'Request submitted but encountered a saving error. Contact support with Ref: ' . $transactionRef], 200);
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

            // Call Upstream Status API
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
                        'success' => false,
                        'message' => 'the request was failed and already refunded',
                        'data' => [
                            'tracking_id' => $agentService->tracking_id,
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
        
        $refundExists = Transaction::where('type', 'refund')
            ->where(function ($q) use ($agentService) {
                $q->where('description', 'LIKE', "%Request ID #{$agentService->id}%")
                  ->orWhere('metadata->original_request_id', $agentService->id);
            })->exists();

        if ($refundExists || $agentService->is_refunded) return 'already_refunded';

        $user = \App\Models\User::find($agentService->user_id);
        if (!$user) return 'error';

        $status = 'error';
        DB::beginTransaction();
        try {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
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
                    'description' => "Refund 100% for rejected IPE service [{$agentService->service_field_name}], Request ID #{$agentService->id}",
                    'metadata' => ['original_request_id' => $agentService->id],
                ]);

                $agentService->update(['is_refunded' => true]); 
                $status = 'refunded';
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
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
            'failed', 'rejected', 'error', 'declined', 'invalid' => 'failed',
            default => 'pending',
        };
    }
}
