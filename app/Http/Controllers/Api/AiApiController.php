<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Wallet;
use App\Models\User;
use Carbon\Carbon;

class AiApiController extends Controller
{
    /**
     * Display the AI API Documentation
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch Service Dynamic prices for all 3 subscription plans
        $service = Cache::remember('service_ai', 3600, function() {
            return Service::where('name', 'AI Services')->first();
        });

        $role = $user->role ?? 'personal';
        
        $prices = [
            'basic' => 1500.00,
            'standard' => 3000.00,
            'premium' => 7000.00
        ];
        
        if ($service) {
            $basicField = Cache::remember('service_field_701', 3600, function() use ($service) {
                return $service->fields()->where('field_code', '701')->first();
            });
            if ($basicField) {
                $prices['basic'] = $basicField->getPriceForUserType($role);
            }

            $standardField = Cache::remember('service_field_702', 3600, function() use ($service) {
                return $service->fields()->where('field_code', '702')->first();
            });
            if ($standardField) {
                $prices['standard'] = $standardField->getPriceForUserType($role);
            }

            $premiumField = Cache::remember('service_field_703', 3600, function() use ($service) {
                return $service->fields()->where('field_code', '703')->first();
            });
            if ($premiumField) {
                $prices['premium'] = $premiumField->getPriceForUserType($role);
            }
        }

        // Prepare active subscription info for frontend rendering
        $subscription = null;
        if ($user && $user->ai_plan) {
            $expiresAt = $user->ai_subscription_expires_at;
            $isActive = $expiresAt && $expiresAt->gt(Carbon::now());
            
            // Treat plan as expired if requests run out (except premium)
            if ($isActive && $user->ai_plan !== 'premium' && $user->ai_remaining_requests <= 0) {
                $isActive = false;
            }

            $subscription = [
                'plan' => $user->ai_plan,
                'remaining' => $user->ai_plan === 'premium' ? 'Unlimited' : $user->ai_remaining_requests,
                'expires_at' => $expiresAt ? $expiresAt->format('Y-m-d H:i') : 'N/A',
                'is_active' => $isActive,
                'status_text' => $isActive ? 'Active' : 'Expired/Depleted',
                'status_badge' => $isActive ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger'
            ];
        }

        return view('api.ai', [
            'user' => $user,
            'prices' => $prices,
            'subscription' => $subscription
        ]);
    }

    /**
     * AI Chat API Endpoint
     */
    public function chat(Request $request)
    {
        // 1. Authenticate User via Bearer Token
        $user = $this->authenticateApiUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing API Token.'
            ], 401);
        }

        // 1b. Check User Status
        if ($user->status !== 'active') { 
             return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active please contact admin'
            ], 403);
        }

        // 2. Validate Request (User must include 'plan' as requested by the user!)
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'plan' => 'required|string|in:basic,standard,premium',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $requestedPlan = $request->input('plan');

        // 2b. User Account-Level Rate Limiting (Protects from Proxy / Flood Abuse)
        $rateLimitMinutes = 1;
        $maxRequests = [
            'basic' => 20,
            'standard' => 40,
            'premium' => 60
        ][$requestedPlan] ?? 20;

        $cacheKey = 'ai_rate_limit:' . $user->id;
        $currentRequests = Cache::get($cacheKey, 0);

        if ($currentRequests >= $maxRequests) {
            return response()->json([
                'status' => 'error',
                'message' => "Rate limit exceeded. Your plan ({$requestedPlan}) allows up to {$maxRequests} requests per minute. Please try again shortly."
            ], 429);
        }

        // Increment rate limit count
        Cache::put($cacheKey, $currentRequests + 1, Carbon::now()->addMinutes($rateLimitMinutes));

        // 3. Verify or automatically renew/subscribe user
        try {
            $subResult = $this->verifyOrRenewSubscription($user, $requestedPlan);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 402); // Return 402 Payment Required for subscription/wallet issues
        }

        // 4. Call DeepSeek API
        try {
            $apiKey = config('services.deepseek.key');
            $baseUrl = config('services.deepseek.base_url');
            $model = 'deepseek-chat';

            $response = Http::timeout(60)->withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post("$baseUrl/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $request->message],
                ],
                'temperature' => 0.7,
                'stream' => false,
            ]);

            if ($response->failed()) {
                Log::error('AI API Upstream Failure', [
                    'user_id' => $user->id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'AI Service is temporarily unavailable.'
                ], 503);
            }

            $aiResult = $response->json();
            $transactionRef = 'AI' . date('is') . strtoupper(Str::random(5));

            // Decrement remaining requests if the plan is basic or standard
            if ($user->ai_plan !== 'premium' && $user->ai_remaining_requests > 0) {
                $user->decrement('ai_remaining_requests');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Successful',
                'data' => [
                    'answer' => $aiResult['choices'][0]['message']['content'] ?? '',
                    'remaining_requests' => $user->ai_plan === 'premium' ? 'unlimited' : $user->ai_remaining_requests,
                    'expires_at' => $user->ai_subscription_expires_at ? $user->ai_subscription_expires_at->toDateTimeString() : null
                ],
                'auto_renewed' => $subResult['renewed'],
                'renew_charge' => $subResult['charge']
            ], 200);

        } catch (\Exception $e) {
            Log::error('AI API Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An internal error occurred.'
            ], 500);
        }
    }

    /**
     * Manual Subscription Endpoint
     */
    public function subscribe(Request $request)
    {
        // 1. Authenticate User strictly via Bearer Token for API security
        $user = $this->authenticateApiUser($request);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing API Token.'
            ], 401);
        }

        // 1b. Check User Status
        if ($user->status !== 'active') { 
             return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active please contact admin'
            ], 403);
        }

        // 2. Validate requested plan
        $validator = Validator::make($request->all(), [
            'plan' => 'required|string|in:basic,standard,premium',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $plan = $request->input('plan');

        try {
            $planCodes = [
                'basic' => '701',
                'standard' => '702',
                'premium' => '703',
            ];

            $planRequests = [
                'basic' => 500,
                'standard' => 1000,
                'premium' => 999999,
            ];

            $fieldCode = $planCodes[$plan];
            
            $service = Service::where('name', 'AI Services')->first();
            if (!$service || !$service->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'AI services are currently unavailable.'
                ], 503);
            }

            $serviceField = ServiceField::where('field_code', $fieldCode)->first();
            if (!$serviceField || !$serviceField->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => "AI plan {$plan} is currently unavailable."
                ], 503);
            }

            // Determine price based on role
            $role = $user->role ?? 'personal';
            $price = $serviceField->getPriceForUserType($role);

            // Prevent double manual subscription if they already have this exact plan active with remaining requests
            $now = Carbon::now();
            $hasActiveSub = $user->ai_plan === $plan && $user->ai_subscription_expires_at && $user->ai_subscription_expires_at->gt($now);
            if ($hasActiveSub && $user->ai_plan !== 'premium' && $user->ai_remaining_requests <= 0) {
                $hasActiveSub = false;
            }

            if ($hasActiveSub) {
                return response()->json([
                    'status' => 'error',
                    'message' => "You already have an active AI " . ucfirst($plan) . " subscription with requests remaining."
                ], 400);
            }

            // Check Wallet Balance
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your wallet is not active.'
                ], 403);
            }

            if ($wallet->balance < $price) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Insufficient wallet balance to subscribe to {$plan} plan. Required: ₦" . number_format($price, 2)
                ], 402);
            }

            // Process Debit and Activate Subscription
            DB::transaction(function () use ($user, $price, $plan, $planRequests, $serviceField, $wallet) {
                $lockedWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                if (!$lockedWallet || $lockedWallet->balance < $price) {
                    throw new \Exception('Insufficient wallet balance during subscription activation.');
                }

                $transactionRef = 'AI' . date('is') . strtoupper(Str::random(5));
                $performedBy = $user->first_name . ' ' . $user->last_name;

                Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'payer_name' => $performedBy,
                    'amount' => $price,
                    'description' => "AI Subscription Purchase - {$serviceField->field_name}",
                    'type' => 'debit',
                    'status' => 'completed',
                    'trans_source' => 'API',
                    'performed_by' => $performedBy,
                    'approved_by' => $user->id,
                    'metadata' => [
                        'service' => 'ai_assistant',
                        'field_code' => $serviceField->field_code,
                        'plan' => $plan,
                        'action' => 'manual_subscription'
                    ],
                ]);

                // Debit Wallet
                $lockedWallet->decrement('balance', $price);

                // Update User Subscription Fields
                $user->ai_plan = $plan;
                $user->ai_remaining_requests = $planRequests[$plan];
                $user->ai_subscription_expires_at = Carbon::now()->addDays(30);
                $user->save();
            });

            return response()->json([
                'status' => 'success',
                'message' => "Successfully subscribed to AI {$plan} plan.",
                'data' => [
                    'plan' => $plan,
                    'expires_at' => $user->ai_subscription_expires_at->toDateTimeString(),
                    'remaining_requests' => $plan === 'premium' ? 'unlimited' : $user->ai_remaining_requests,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('AI Subscription Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?? 'An error occurred during subscription.'
            ], 500);
        }
    }

    /**
     * Unsubscribe from active AI subscription
     */
    public function unsubscribe(Request $request)
    {
        // 1. Authenticate User strictly via Bearer Token for API security
        $user = $this->authenticateApiUser($request);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing API Token.'
            ], 401);
        }

        // 2. Check if user is active
        if ($user->status !== 'active') { 
             return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active please contact admin'
            ], 403);
        }

        // 3. Check if user has active subscription
        if (!$user->ai_plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have an active AI subscription to cancel.'
            ], 400);
        }

        try {
            $oldPlan = $user->ai_plan;

            // Clear subscription fields on User
            $user->ai_plan = null;
            $user->ai_remaining_requests = 0;
            $user->ai_subscription_expires_at = null;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => "Successfully unsubscribed from AI {$oldPlan} plan. No refund has been issued.",
                'data' => [
                    'plan' => null,
                    'remaining_requests' => 0,
                    'expires_at' => null
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('AI Unsubscribe Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during unsubscription.'
            ], 500);
        }
    }

    /**
     * Verify or renew subscription
     */
    private function verifyOrRenewSubscription(User $user, string $requestedPlan)
    {
        $now = Carbon::now();

        $planCodes = [
            'basic' => '701',
            'standard' => '702',
            'premium' => '703',
        ];

        $planRequests = [
            'basic' => 500,
            'standard' => 1000,
            'premium' => 999999, // practically unlimited
        ];

        $fieldCode = $planCodes[$requestedPlan] ?? null;
        if (!$fieldCode) {
            throw new \Exception("Invalid plan selected.");
        }

        // Fetch Service and ServiceField
        $service = Service::where('name', 'AI Services')->first();
        if (!$service || !$service->is_active) {
            throw new \Exception("AI services are currently unavailable.");
        }

        $serviceField = ServiceField::where('field_code', $fieldCode)->first();
        if (!$serviceField || !$serviceField->is_active) {
            throw new \Exception("AI plan {$requestedPlan} is currently unavailable.");
        }

        // Determine price
        $role = $user->role ?? 'personal';
        $price = $serviceField->getPriceForUserType($role);

        // Check if the user currently has a valid active subscription
        $hasActiveSub = $user->ai_plan && $user->ai_subscription_expires_at && $user->ai_subscription_expires_at->gt($now);
        
        // If they are on a limited plan, check if they have requests remaining
        if ($hasActiveSub && $user->ai_plan !== 'premium' && $user->ai_remaining_requests <= 0) {
            $hasActiveSub = false; // Treat as inactive to trigger auto-renewal
        }

        // If they do not have an active subscription, or if they have expired/run out
        if (!$hasActiveSub) {
            // Check Wallet Balance
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->status !== 'active') {
                throw new \Exception("Your wallet is not active.");
            }

            if ($wallet->balance < $price) {
                if ($user->ai_plan) {
                    throw new \Exception("Your AI subscription ({$user->ai_plan}) has expired/run out of requests, and automatic renewal failed due to insufficient wallet balance. Please fund your wallet to renew.");
                } else {
                    throw new \Exception("You do not have an active AI subscription. Auto-activation for the '{$requestedPlan}' plan failed due to insufficient wallet balance. Please fund your wallet (Required: ₦" . number_format($price, 2) . ").");
                }
            }

            // Process Debit and Activate Subscription
            DB::transaction(function () use ($user, $price, $requestedPlan, $planRequests, $serviceField, $wallet) {
                $lockedWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                if (!$lockedWallet || $lockedWallet->balance < $price) {
                    throw new \Exception('Insufficient wallet balance during subscription activation.');
                }

                $transactionRef = 'AI' . date('is') . strtoupper(Str::random(5));
                $performedBy = $user->first_name . ' ' . $user->last_name;

                Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'payer_name' => $performedBy,
                    'amount' => $price,
                    'description' => "AI Auto-Subscription - {$serviceField->field_name}",
                    'type' => 'debit',
                    'status' => 'completed',
                    'trans_source' => 'API',
                    'performed_by' => $performedBy,
                    'approved_by' => $user->id,
                    'metadata' => [
                        'service' => 'ai_assistant',
                        'field_code' => $serviceField->field_code,
                        'plan' => $requestedPlan,
                        'action' => $user->ai_plan ? 'auto_renewal' : 'first_subscription'
                    ],
                ]);

                // Debit Wallet
                $lockedWallet->decrement('balance', $price);

                // Update User Subscription Fields
                $user->ai_plan = $requestedPlan;
                $user->ai_remaining_requests = $planRequests[$requestedPlan];
                $user->ai_subscription_expires_at = Carbon::now()->addDays(30);
                $user->save();
            });

            return [
                'renewed' => true,
                'charge' => $price
            ];
        }

        // If user already has an active subscription, but they requested a DIFFERENT plan than current active plan
        if ($user->ai_plan !== $requestedPlan) {
            $remaining = $user->ai_plan === 'premium' ? 'unlimited' : $user->ai_remaining_requests;
            $expires = $user->ai_subscription_expires_at->format('Y-m-d H:i:s');
            throw new \Exception("You already have an active '{$user->ai_plan}' AI subscription with {$remaining} requests remaining (Expires: {$expires}). Please continue utilizing your active plan by setting 'plan' to '{$user->ai_plan}'.");
        }

        // Active subscription for requested plan is valid
        return [
            'renewed' => false,
            'charge' => 0
        ];
    }

    /**
     * Authenticate User via Bearer Token manually
     */
    private function authenticateApiUser(Request $request)
    {
        if ($request->user()) {
            return $request->user();
        }
        $token = $request->bearerToken() ?? $request->header('Authorization');
        if (is_string($token) && strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        
        $token = is_string($token) ? trim($token) : '';
        if (empty($token) || strlen($token) < 20) {
            return null;
        }

        return User::where('api_token', $token)->first();
    }
}
