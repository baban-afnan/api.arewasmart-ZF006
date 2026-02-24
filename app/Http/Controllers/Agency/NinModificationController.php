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

class NinModificationController extends Controller
{
    /**
     * Display the NIN Modification API Documentation.
     * Only accessible to logged-in users.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access API documentation.');
        }

        $role = $user->role ?? 'user';

        // Get Modification Service
        $ninService = Service::where('name', 'NIN Modification')
            ->orWhere('name', 'NIN MODIFICATION')
            ->first();

        // Fetch service fields
        $fields = $ninService ? $ninService->fields : collect();
        $services = collect();

        // Specific codes requested by user
        $targetCodes = ['032', '033', '034', '035', '037'];

        foreach ($fields as $field) {
            // Filter specific codes
            if (!in_array($field->field_code, $targetCodes)) continue;
            if (!$field->is_active) continue;

            $price = method_exists($field, 'getPriceForUserType') 
                ? $field->getPriceForUserType($role) 
                : ($field->prices()->where('user_type', $role)->value('price') ?? $field->base_price);

            $services->push((object)[
                'id' => $field->id, 
                'name' => $field->field_name, 
                'code' => $field->field_code, 
                'price' => $price, 
                'type' => 'Modification'
            ]);
        }

        return view('nin.modification', compact('user', 'services'));
    }

    /**
     * Process NIN Modification Request (API Only).
     */
    public function store(Request $request)
    {
        // 1. Authenticate user
        $user = $this->authenticateUser($request);
        if (!$user) {
             return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        // 2. Validate request
        $rules = [
            'field_code' => 'required',
            'nin' => 'required|digits:11',
            'modification_data' => 'nullable|array',
            'description' => 'required|string|max:500' // Required as per standardization
        ];

        $validator = Validator::make($request->all(), $rules);

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
            return response()->json([
                'success' => false,
                'message' => 'Service or Field is not active'
            ], 503);
        }

        // Additional Modification Validation
        $modData = $request->modification_data ?? [];
        if (in_array($fieldCode, ['035'])) { 
            $requiredDobFields = [
                'first_name', 'surname', 'gender', 'new_dob', 'nationality', 'state_of_origin', 
                'residence_state', 'phone_number', 'place_of_birth'
            ];

            foreach ($requiredDobFields as $field) {
                if (empty($modData[$field])) {
                     return response()->json(['success' => false, 'message' => "Missing required field for DOB Update: {$field}"], 400);
                }
            }
        }
        
        if (empty($modData) && empty($request->description)) {
             return response()->json(['success' => false, 'message' => 'Please provide modification details or description.'], 400);
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
            $transactionRef = 'M1' . strtoupper(Str::random(10));
            $performedBy = trim($user->first_name . ' ' . $user->last_name);

            // 8. Create transaction (pending or success)
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id'        => $user->id,
                'amount'         => $servicePrice,
                'description'    => "NIN modification for {$serviceField->field_name}",
                'type'           => 'debit',
                'status'         => 'completed',
                'trans_source'   => 'API',
                'performed_by'   => $performedBy,
                'metadata'       => [
                    'service'          => $service->name,
                    'service_field'    => $serviceField->field_name,
                    'field_code'       => $serviceField->field_code,
                    'nin'              => $request->nin,
                    'details'          => $request->modification_data ?? $request->description
                ],
            ]);

            // 9. Debit wallet
            $wallet->decrement('balance', $servicePrice);

            // 10. Create service record and send to api if the service required api
            $description = $request->description ?? "NIN Modification Request ({$serviceField->field_name})";

            $agentService = AgentService::create([
                'reference'          => $transactionRef,
                'user_id'            => $user->id,
                'service_field_id'   => $serviceField->id,
                'service_id'         => $service->id,
                'field_code'         => $serviceField->field_code,
                'amount'             => $servicePrice,
                'service_name'       => $service->name,
                'service_field_name' => $serviceField->field_name,
                'nin'                => $request->nin,
                'description'        => $description,
                'modification_data'  => $request->modification_data,
                'performed_by'       => $performedBy,
                'transaction_id'     => $transaction->id,
                'submission_date'    => now(),
                'status'             => 'pending',
                'service_type'       => 'NIN MODIFICATION',
                'comment'            => 'Request submitted, pending processing',
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
                    'amount_charged' => $servicePrice
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NIN Modification API failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Submission failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check Status (API Only).
     */
    public function checkStatus(Request $request)
    {
        try {
            // Validate & Auth
            $user = $this->authenticateUser($request);
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);

            $validator = Validator::make($request->all(), [
                'reference' => 'nullable|string',
                'nin' => 'nullable|digits:11',
            ]);

            if ($validator->fails()) {
                 return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
            }

            if (!$request->reference && !$request->nin) {
                 return response()->json(['success' => false, 'message' => 'Provide either reference or nin.'], 400);
            }
            
            $query = AgentService::where('user_id', $user->id);

            if ($request->reference) {
                $query->where('reference', $request->reference);
            } elseif ($request->nin) {
                $query->where('nin', $request->nin);
            }

            // Get latest if multiple
            $agentService = $query->latest('created_at')->first();

            if (!$agentService) {
                    return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
            }

            // Prepare response data
            // The user mentioned checking status gives information on 'comment' column and 'file_url'
            return response()->json([
                'success' => true,
                'data' => [
                    'reference' => $agentService->reference,
                    'nin' => $agentService->nin,
                    'service' => $agentService->service_field_name,
                    'status' => $agentService->status,
                    'comment' => $agentService->comment,
                    'file_url' => $agentService->file_url,
                    'submission_date' => $agentService->submission_date
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Status Check Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to check status.'], 400);
        }
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

        if (!$apiToken) return null;

        return \App\Models\User::where('api_token', $apiToken)->first();
    }
}
