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
        // 1. Validation
        $rules = [
            'field_code' => 'required',
            'nin' => 'required|digits:11',
            'modification_data' => 'nullable|array',
            'description' => 'nullable|string|max:500'
        ];

        $validator = Validator::make($request->all(), $rules);

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
        if (!$service || !$service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service is not active'
            ], 503);
        }

        // Additional Validation based on Field Code
        $modData = $request->modification_data ?? [];
        
        // Date of birth update below 5 year (035) logic could be similar to standard DOB if fields overlap, 
        // but typically 035 might need less or specific proofs. 
        // Based on user "DOB Modification Wizard" which seems generic for DOB changes:
        if (in_array($fieldCode, ['035'])) { 
            // The user provided a wizard for DOB, assuming '035' (or general DOB update) uses those fields.
            // Note: The user mentioned "Date of birth update below 5 year" is 035. 
            // If there's an adult DOB update, it might be a different code not listed or implies 035 is the main one used here.
            // Let's validate the key fields from the wizard if the code matches a DOB update.
            
            $requiredDobFields = [
                'first_name', 'surname', 'gender', 'marital_status', 
                'new_dob', 'nationality', 'state_of_origin', 'lga_of_origin', 'town_of_origin',
                'residence_state', 'residence_lga', 'residence_town', 'residence_address', 'phone_number',
                'place_of_birth', 'state_of_birth', 'lga_of_birth',
                'father_surname', 'father_firstname', 'mother_surname', 'mother_firstname'
            ];

            foreach ($requiredDobFields as $field) {
                if (empty($modData[$field])) {
                     return response()->json(['success' => false, 'message' => "Missing required field for DOB Update: {$field}"], 400);
                }
            }
        }
        
        // For other fields like Name Correction (032), Phone (033), ensure at least some description or data is present.
        if (empty($modData) && empty($request->description)) {
             return response()->json(['success' => false, 'message' => 'Please provide modification details or description.'], 400);
        }

        // 3. User Authentication
        $user = $this->authenticateUser($request);
        if (!$user) {
             return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        // 3b. Check User Status 
        // Commenting out as default status is null for new users, blocking requests.
        /* if ($user->status !== 'active') { 
             return response()->json([
                'success' => false,
                'message' => 'Your account is not active please contact admin'
            ], 403);
        } */

        $service = $serviceField->service;
        // Check if main service or specific field is active
        // User requested: "allow... only if the service_fields is_active that 1"
        if (!$service || !$service->is_active || !$serviceField->is_active) {
            return response()->json([
                'success' => false, // Maintaining existing JSON structure for this controller? User snippet had 'status' => 'error'.
                // User snippet: 'status' => 'error'. But existing controller uses 'success' => false. 
                // I will use user's snippet structure for 503.
                'status' => 'error',
                'message' => 'Service is not active'
            ], 503);
        }

        // 4. Wallet Check
        $role = $user->role ?? 'user';
        
        $servicePrice = method_exists($serviceField, 'getPriceForUserType') 
            ? $serviceField->getPriceForUserType($role) 
            : ($serviceField->prices()->where('user_type', $role)->value('price') ?? $serviceField->base_price);

        // Safety check for price
        if ($servicePrice === null) {
             return response()->json(['success' => false, 'message' => 'Service price not configured for your user type.'], 400);
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        // Check wallet status and balance
        if (!$wallet || $wallet->status !== 'active') {
             return response()->json(['success' => false, 'message' => 'Wallet inactive or not found.'], 400);
        }

        if ($wallet->balance < $servicePrice) {
            return response()->json(['success' => false, 'message' => 'Insufficient wallet balance.'], 400);
        }

        // 5. Create Transaction & Service Record
        DB::beginTransaction();

        try {
            // Generate Reference
            $transactionRef = 'M1' . strtoupper(Str::random(10));
            $performedBy = trim($user->first_name . ' ' . $user->last_name);

            // Create Transaction
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

            // Determine description
            $description = $request->description ?? "NIN Modification Request ({$serviceField->field_name})";

            // Create NIN Modification record
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
                'modification_data'  => $request->modification_data, // Stores JSON if cast in model, or text
                'performed_by'       => $performedBy,
                'transaction_id'     => $transaction->id,
                'submission_date'    => now(),
                'status'             => 'pending',
                'service_type'       => 'NIN MODIFICATION', // Consistent type
            ]);

            // Debit Wallet
            $wallet->decrement('balance', $servicePrice);

            DB::commit();

            Log::info('NIN Modification API submitted successfully', [
                'user_id' => $user->id,
                'transaction_ref' => $transactionRef,
            ]);

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
            Log::error('NIN Modification API failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Submission failed. Please try again.'], 400);
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
