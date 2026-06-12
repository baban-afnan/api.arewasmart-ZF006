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
            'field_code' => ['required', 'string', 'regex:/015/'],
            'nin' => 'required|digits:11',
            'description' => 'nullable|string|max:255',
        ], [
            'field_code.regex' => 'The selected field code is not authorized for NIN Validation requests.'
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

        // 3. Check service active (Strictly restricted to Validation codes)
        $fieldCode = $request->field_code;
        $serviceField = ServiceField::with('service')
            ->where('field_code', $fieldCode)
            ->where('field_code', 'LIKE', '%015%')
            ->first();
        
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

        if ($servicePrice === null || $servicePrice <= 0) {
            return response()->json(['success' => false, 'message' => 'Service price not configured or invalid.'], 400);
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

            // Generate Unique Reference
            $performedBy = $user->first_name . ' ' . $user->last_name;
            do {
                $transactionRef = 'val' . date('is') . strtoupper(Str::random(5));
            } while (Transaction::where('transaction_ref', $transactionRef)->exists());

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

            // 10. Create AgentService record in database with pending status
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
                'status' => 'pending',
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'comment' => 'Request submitted, pending processing',
                'performed_by' => $performedBy,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Validation Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System Error: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request submitted, pending processing',
            'data' => [
                'reference' => $agentService->reference,
                'trx_ref' => $transactionRef,
                'status' => 'pending',
                'nin' => $request->nin,
                'comment' => 'Request submitted, pending processing'
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

            return response()->json([
                'success' => true,
                'nin' => $agentService->nin,
                'status' => $agentService->status,
                'comment' => $agentService->comment,
                'message' => 'Status checked.'
            ]);

        } catch (\Exception $e) {
            Log::error('Validation Status Check Error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
                'nin' => $request->nin ?? $id,
            ]);
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
}
