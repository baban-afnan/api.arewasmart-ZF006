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

class BvnCrmController extends Controller
{
    // Defined Service Codes
    private const CRM_CODES = ['021'];

    /**
     * Display the BVN CRM API Documentation.
     * Only accessible to logged-in users.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access API documentation.');
        }

        $role = $user->role ?? 'user';

        // Fetch active fields matching CRM codes
        $fields = ServiceField::with(['service', 'prices'])
            ->whereIn('field_code', self::CRM_CODES)
            ->where('is_active', 1)
            ->whereHas('service', function($q) {
                $q->where('is_active', 1);
            })
            ->get();

        $availableServices = collect();

        foreach ($fields as $field) {
            $price = $this->calculateServicePrice($field, $role);

            $availableServices->push((object)[
                'id' => $field->id, 
                'name' => $field->field_name, 
                'code' => $field->field_code, 
                'price' => $price, 
                'bank' => $field->service->name,
                'category' => 'crm',
                'type' => 'CRM'
            ]);
        }

        return view('bvn.crm', compact('user', 'availableServices'));
    }

    /**
     * Process BVN CRM Request (API Only).
     */
    public function store(Request $request)
    {
        // 1. Authenticate user
        $user = $this->authenticateUser($request);
        if (!$user) {
             return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        // 2. Validate request
        $validator = Validator::make($request->all(), [
            'field_code'        => 'required',
            'ticket_id'         => 'required|digits:8',
            'batch_id'          => 'required|digits:7',
        ]);

        if ($validator->fails()) {
             return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // 3. Check service active
        $serviceField = ServiceField::with('service')->where('field_code', $request->field_code)->first();
        
        if (!$serviceField) {
             return response()->json(['success' => false, 'message' => 'Invalid Service Field Code.'], 400);
        }

        $service = $serviceField->service;
        
        if (!$service || !$service->is_active || !$serviceField->is_active) {
            return response()->json(['success' => false, 'message' => 'Service or Field is not active'], 503);
        }

        // 4. Calculate price
        $role = $user->role ?? 'user';
        $servicePrice = $this->calculateServicePrice($serviceField, $role);

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
            $transactionRef = 'CRM' . strtoupper(Str::random(10)); // CRM for CRM
            $performedBy = trim($user->first_name . ' ' . $user->last_name);

            // 8. Create transaction (pending or success)
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id'        => $user->id,
                'amount'         => $servicePrice,
                'description'    => "BVN CRM for {$serviceField->field_name}",
                'type'           => 'debit',
                'status'         => 'completed',
                'trans_source'   => 'API',
                'performed_by'   => $performedBy,
                'metadata'       => [
                    'service'          => $service->name,
                    'service_field'    => $serviceField->field_name,
                    'field_code'       => $serviceField->field_code,
                    'ticket_id'        => $request->ticket_id,
                    'batch_id'         => $request->batch_id,
                ],
            ]);

            // 9. Debit wallet
            $wallet->decrement('balance', $servicePrice);

            // 10. Create service record and send to api if the service required api
            $agentService = AgentService::create([
                'reference'        => $transactionRef,
                'user_id'          => $user->id,
                'service_id'       => $service->id, 
                'service_field_id' => $serviceField->id,
                'service_name'     => $service->name,
                'field_code'       => $serviceField->field_code,
                'field_name'       => $serviceField->field_name,
                'bank'             => $service->name,
                'ticket_id'        => $request->ticket_id,
                'batch_id'         => $request->batch_id,
                'amount'           => $servicePrice,
                'transaction_id'   => $transaction->id,
                'submission_date'  => now(),
                'status'           => 'pending',
                'service_type'     => 'bvn_crm',
                'comment'          => 'Request submitted, pending processing',
                'performed_by'     => $performedBy,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Request submitted successfully',
                'data' => [
                    'reference' => $agentService->reference,
                    'trx_ref' => $transactionRef,
                    'status' => 'pending',
                    'service' => $serviceField->field_name,
                    'code' => $serviceField->field_code,
                    'amount_charged' => $servicePrice
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BVN CRM API failed', ['error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Submission failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check Status (API Only).
     */
    public function checkStatus(Request $request)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);

            if (!$request->reference && !$request->ticket_id && !$request->batch_id) {
                 return response()->json(['success' => false, 'message' => 'Provide either reference, ticket_id, or batch_id.'], 400);
            }
            
            $query = AgentService::where('user_id', $user->id)
                ->where('service_type', 'bvn_crm');

            if ($request->reference) {
                $query->where('reference', $request->reference);
            } elseif ($request->ticket_id) {
                $query->where('ticket_id', $request->ticket_id);
            } elseif ($request->batch_id) {
                $query->where('batch_id', $request->batch_id);
            }

            $agentService = $query->latest('created_at')->first();

            if (!$agentService) {
                return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reference'       => $agentService->reference,
                    'ticket_id'       => $agentService->ticket_id,
                    'batch_id'        => $agentService->batch_id,
                    'service'         => $agentService->service_field_name ?? $agentService->field_name,
                    'status'          => $agentService->status,
                    'comment'         => $agentService->comment,
                    'file_url'        => $agentService->file_url,
                    'submission_date' => $agentService->submission_date
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to check status.'], 400);
        }
    }

    /**
     * Helper: Authenticate User via Bearer Token or api_token param
     */
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

        if (!$apiToken) return null;

        return \App\Models\User::where('api_token', $apiToken)->first();
    }

    /**
     * Helper: Calculate Service Price based on User Role
     */
    private function calculateServicePrice($field, $role)
    {
        if (method_exists($field, 'getPriceForUserType')) {
            return $field->getPriceForUserType($role);
        }
        
        return $field->prices()->where('user_type', $role)->value('price') ?? $field->base_price;
    }
}