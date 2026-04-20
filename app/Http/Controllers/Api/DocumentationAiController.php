<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\SmeData;
use Illuminate\Support\Facades\DB;

class DocumentationAiController extends Controller
{
    /**
     * Handle AI Chat requests for documentation
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'current_url' => 'nullable|url',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // 1. Gather dynamic platform context
        $context = $this->getAiContext($user, $request->current_url);

        // 2. Call DeepSeek API
        try {
            $apiKey = config('services.deepseek.key');
            $baseUrl = config('services.deepseek.base_url');

            $response = Http::timeout(60)->withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post("$baseUrl/chat/completions", [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'system', 'content' => $context],
                    ['role' => 'user', 'content' => $request->message],
                ],
                'temperature' => 0.7,
                'stream' => false,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'AI is busy try again'
                ], 503);
            }

            return response()->json([
                'status' => 'success',
                'answer' => $response->json()['choices'][0]['message']['content'] ?? 'I am sorry, but I could not generate a response.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'AI is busy try again'
            ], 500);
        }
    }

    /**
     * Construct the system prompt with DB context
     */
    private function getAiContext($user, $currentUrl = null)
    {
        $userName = $user->first_name . ' ' . $user->last_name;
        $role = $user->role ?? 'user';
        $locationContext = $currentUrl ? "The user is currently viewing this page: $currentUrl" : "The user is browsing the platform.";

        // Fetch pricing context for all active generic services (Identity, etc.)
        $services = Service::with(['fields.prices'])->where('is_active', true)
            ->whereNotIn('name', ['Data', 'SME Data'])
            ->get();
        $pricingList = "";

        foreach ($services as $service) {
            $pricingList .= "\n### Service: {$service->name}\n";
            foreach ($service->fields as $field) {
                if (!$field->is_active) continue;
                
                // Get price for user role
                $price = $field->getPriceForUserType($role);
                $pricingList .= "- {$field->field_name} (Code: {$field->field_code}): ₦" . number_format($price, 2) . "\n";
            }
        }

        // 3. Fetch Data Variations (Regular/Gifting) & Commissions
        $dataService = Service::with(['fields.prices'])->where('name', 'Data')->first();
        $dataCommissions = [];
        if ($dataService) {
            foreach ($dataService->fields as $field) {
                $dataCommissions[$field->field_code] = $field->getPriceForUserType($role);
            }
        }

        $fieldCodeMap = [
            'mtn-data'      => '104',
            'airtel-data'   => '105',
            'glo-data'      => '106',
            'etisalat-data' => '107',
        ];

        $dataVariations = DB::table('data_variations')
            ->where('status', 'enabled')
            ->get()
            ->groupBy('service_name');
        
        $dataList = "";
        foreach ($dataVariations as $network => $plans) {
            $networkKey = strtolower($network);
            $fieldCode = $fieldCodeMap[$networkKey] ?? ($fieldCodeMap[$networkKey . '-data'] ?? null);
            $commission = $dataCommissions[$fieldCode] ?? 0;

            $dataList .= "\n#### Network: " . strtoupper($network) . " (Cashback/Commission: {$commission}%)\n";
            foreach ($plans as $plan) {
                $dataList .= "- {$plan->name}: ₦" . number_format($plan->variation_amount, 2) . " (Code: {$plan->variation_code})\n";
            }
        }

        // 4. Fetch SME Data Plans (Role-Aware via calculatePriceForRole)
        $smePlans = SmeData::where('status', 'enabled')->get()->groupBy('network');
        $smeList = "";
        foreach ($smePlans as $network => $plans) {
            $smeList .= "\n#### Network: " . strtoupper($network) . "\n";
            foreach ($plans as $plan) {
                $price = $plan->calculatePriceForRole($role);
                $smeList .= "- {$plan->size} ({$plan->plan_type}): ₦" . number_format($price, 2) . " (Code: {$plan->data_id}, Validity: {$plan->validity})\n";
            }
        }

        return "
You are the **Arewa Smart AI Support Assistant**. Your goal is to help developers integrate the Arewa Smart API professionally and easily.

### IMPORTANT PRICING RULE:
- All prices listed below are **already tailored** to the current user's role: **{$role}**.
- For 'Generic Services' and 'SME Data', the displayed price is exactly what the user will be charged.
- For 'Data Bundles (Regular)', the user is charged the face value but receives the listed **Cashback/Commission** instantly.
- Always quote the correct price/commission for this role.

### CURRENT USER CONTEXT:
- **User Name**: {$userName}
- **Role**: {$role} (Use this to provide correct pricing)
- **API Token**: `{$user->api_token}` (Example only, reinforce that they must keep it secret)
- **Location**: {$locationContext}

### PLATFORM OVERVIEW:
Arewa Smart is a top-tier Nigeria API provider for Identity Verification (NIN, BVN, TIN) and Utility Payments.
- **Base URL**: " . url('/api/v1') . "
- **Auth**: 'Authorization: Bearer <API_TOKEN>'

### LIVE PRICING & PLANS (FOR THIS USER):
- **User Role**: {$role}
- **Generic Services**: {$pricingList}

### DATA BUNDLES (REGULAR):
{$dataList}

### SME/SPECIAL DATA PLANS:
{$smeList}

### GUIDELINES FOR THE USER:
1. **Security**: Always use HTTPS. Store API tokens in environment variables (.env), never hardcode them.
2. **Billing**: Users are charged per successful verification (Chargeable Response Codes: 00000000).
3. **Application**: To apply for new API services or higher limits, contact Support via WhatsApp at +2347037343660.

### YOUR INSTRUCTIONS:
- You must always advise the user on the **correct price** for their role ({$role}) based on the list above.
- Provide **code snippets** in languages like PHP (Laravel), Python, or JavaScript when asked for integration help.
- If they ask about applying for services, point them to the **WhatsApp Support link**.
- Be concise, helpful, and technically accurate.
- If you don't know something specifically about the system settings not listed here, advise them to contact the admin.
";
    }
}
