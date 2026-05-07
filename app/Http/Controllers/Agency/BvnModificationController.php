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

class BvnModificationController extends Controller
{
    // Defined Service Codes
    private const KEYSTONE_CODES = ['67', '68', '69', '70', '71', '72', '73'];
    private const FIRST_BANK_CODES = ['003', '004', '005', '006', '007', '008', '009', '010', '060', '050', '3', '4', '5', '6', '7', '8', '9', '10', '60', '50'];
    private const AGENCY_CODES = ['022', '023', '024', '025', '026', '027', '028', '66'];

    /**
     * Display the BVN Modification API Documentation.
     * Only accessible to logged-in users.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access API documentation.');
        }

        $role = $user->role ?? 'user';

        // Merge all allowed codes
        $allowedCodes = array_merge(self::KEYSTONE_CODES, self::FIRST_BANK_CODES, self::AGENCY_CODES);

        // Fetch active fields matching codes, with their parent service
        $fields = ServiceField::with(['service', 'prices'])
            ->whereIn('field_code', $allowedCodes)
            ->where('is_active', 1)
            ->whereHas('service', function($q) {
                $q->where('is_active', 1);
            })
            ->get();

        $availableServices = collect();

        foreach ($fields as $field) {
            $price = $this->calculateServicePrice($field, $role);
            $category = $this->getCategoryByCode($field->field_code);

            $availableServices->push((object)[
                'id' => $field->id, 
                'name' => $field->field_name, 
                'code' => $field->field_code, 
                'price' => $price, 
                'bank' => $field->service->name,
                'category' => $category,
                'type' => 'Modification'
            ]);
        }

        return view('bvn.modification', compact('user', 'availableServices'));
    }

    /**
     * Process BVN Modification Request (API Only).
     */
    public function store(Request $request)
    {
        // 1. Authenticate user
        $user = $this->authenticateUser($request);
        if (!$user) {
             return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        // 2. Validate request
        $allowedCodes = array_merge(self::KEYSTONE_CODES, self::FIRST_BANK_CODES, self::AGENCY_CODES);
        $validator = Validator::make($request->all(), [
            'field_code'        => ['required', 'in:' . implode(',', $allowedCodes)],
            'bvn'               => 'required|digits:11',
            'nin'               => 'required|digits:11',
            'modification_data' => 'nullable|array',
            'description'       => 'required',
            'surname'           => 'nullable|string',
            'firstname'         => 'nullable|string',
            'middlename'        => 'nullable|string',
        ], [
            'field_code.in' => 'The selected field code is not authorized for BVN Modification requests.'
        ]);

        if ($validator->fails()) {
             return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // 3. Check service active (Strictly restricted to authorized codes)
        $serviceField = ServiceField::with('service')
            ->where('field_code', $request->field_code)
            ->whereIn('field_code', $allowedCodes)
            ->first();
        
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
            do {
                $transactionRef = 'B1' . strtoupper(Str::random(10));
            } while (Transaction::where('transaction_ref', $transactionRef)->exists());
            
            $performedBy = trim($user->first_name . ' ' . $user->last_name);
            
            // Handle Description & Name Extraction
            $descriptionInput = $request->description;
            $surname = $request->surname;
            $firstname = $request->firstname;
            $middlename = $request->middlename;
            $finalDescription = $descriptionInput;

            if (is_array($descriptionInput)) {
                $surname = $descriptionInput['surname'] ?? $surname;
                $firstname = $descriptionInput['firstname'] ?? $firstname;
                $middlename = $descriptionInput['middlename'] ?? $middlename;
                $finalDescription = json_encode($descriptionInput);
            } elseif ($surname || $firstname) {
                // If distinct name fields are provided, structure them as JSON
                $finalDescription = json_encode([
                    'surname'    => $surname,
                    'firstname'  => $firstname,
                    'middlename' => $middlename
                ]);
            }

            // 8. Create transaction (pending or success)
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id'        => $user->id,
                'amount'         => $servicePrice,
                'description'    => "BVN modification for {$serviceField->field_name}",
                'type'           => 'debit',
                'status'         => 'completed',
                'trans_source'   => 'API',
                'performed_by'   => $performedBy,
                'metadata'       => [
                    'service'          => $service->name,
                    'service_field'    => $serviceField->field_name,
                    'field_code'       => $serviceField->field_code,
                    'bvn'              => $request->bvn,
                    'nin'              => $request->nin,
                    'surname'          => $surname,
                    'firstname'        => $firstname,
                    'middlename'       => $middlename,
                    'details'          => $request->modification_data ?? $finalDescription
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
                'bvn'              => $request->bvn,
                'nin'              => $request->nin,
                'description'      => $finalDescription,
                'amount'           => $servicePrice,
                'transaction_id'   => $transaction->id,
                'submission_date'  => now(),
                'status'           => 'pending',
                'service_type'     => 'bvn_modification',
                'comment'          => 'Request submitted, pending processing',
                'performed_by'     => $performedBy,
                'modification_data'=> $request->modification_data,
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
            Log::error('BVN Modification API failed', ['error' => $e->getMessage()]);

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

            if (!$request->reference && !$request->bvn && !$request->nin) {
                 return response()->json(['success' => false, 'message' => 'Provide either reference, bvn, or nin.'], 400);
            }
            
            $query = AgentService::where('user_id', $user->id)
                ->where('service_type', 'bvn_modification'); // Ensure case matches storage

            if ($request->reference) {
                $query->where('reference', $request->reference);
            } elseif ($request->bvn) {
                $query->where('bvn', $request->bvn);
            } elseif ($request->nin) {
                $query->where('nin', $request->nin);
            }

            $agentService = $query->latest('created_at')->first();

            if (!$agentService) {
                return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reference'       => $agentService->reference,
                    'bvn'             => $agentService->bvn,
                    'nin'             => $agentService->nin,
                    'service'         => $agentService->service_field_name,
                    'status'          => $agentService->status,
                    'comment'         => $agentService->comment,
                    'description'     => $agentService->description,
                    'file_url'        => $agentService->file_url,
                    'submission_date' => $agentService->submission_date
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('BVN Modification Status Check failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
                'request' => $request->only(['reference', 'bvn', 'nin'])
            ]);
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

    /**
     * Helper: Determine Category based on Field Code
     */
    private function getCategoryByCode($code)
    {
        if (in_array($code, self::KEYSTONE_CODES)) {
            return 'keystone';
        } 
        
        if (in_array($code, self::FIRST_BANK_CODES)) {
            return 'firstbank';
        } 
        
        if (in_array($code, self::AGENCY_CODES)) {
            return 'agency';
        }

        return 'other';
    }
}
