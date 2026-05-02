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
        
        // Fetch Service Price for Documentation (Service: AI Services, Field: 700)
        $service = Cache::remember('service_ai', 3600, function() {
            return Service::where('name', 'AI Services')->first();
        });

        $price = 0;
        
        if ($service) {
            $serviceField = Cache::remember('service_field_700', 3600, function() use ($service) {
                return $service->fields()
                    ->where('field_code', '700')
                    ->first();
            });
                
            if ($serviceField) {
                $role = $user->role ?? 'personal'; 
                $price = $serviceField->getPriceForUserType($role);
            }
        }

        return view('api.ai', [
            'user' => $user,
            'aiPrice' => $price
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

        // 2. Validate Request
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
            'model' => 'nullable|string|in:deepseek-chat,deepseek-reasoner',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // 3. Get AI Service
        $service = Cache::remember('service_ai', 3600, function() {
            return Service::where('name', 'AI Services')->first();
        });

        if (!$service || !$service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'AI service is currently unavailable.'
            ], 503);
        }

        // 4. Get AI ServiceField (Code 700)
        $serviceField = Cache::remember('service_field_700', 3600, function() use ($service) {
            return $service->fields()
                ->where('field_code', '700')
                ->first();
        });

        if (!$serviceField || !$serviceField->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'AI API service field (700) is not active.'
            ], 503);
        }

        // 5. Determine service price
        $role = $user->role ?? 'personal';
        $servicePrice = $serviceField->getPriceForUserType($role);

        // 6. Check Wallet Balance
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->status !== 'active') {
             return response()->json([
                'status' => 'error',
                'message' => 'Your wallet is not active.'
            ], 403);
        }

        if ($wallet->balance < $servicePrice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance. Please fund your wallet.'
            ], 402);
        }

        // 7. Call DeepSeek API
        try {
            $apiKey = config('services.deepseek.key');
            $baseUrl = config('services.deepseek.base_url');
            $model = $request->input('model', 'deepseek-chat');

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
            $performedBy = $user->first_name . ' ' . $user->last_name;

            // 8. Process Transaction and Debit Wallet
            return DB::transaction(function () use ($servicePrice, $aiResult, $user, $serviceField, $transactionRef, $performedBy) {
                // Lock wallet for update
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

                if (!$wallet || $wallet->balance < $servicePrice) {
                    throw new \Exception('Insufficient wallet balance.');
                }

                // Create Transaction
                Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'payer_name' => $performedBy,
                    'amount' => $servicePrice,
                    'description' => "AI API Usage - {$serviceField->field_name}",
                    'type' => 'debit',
                    'status' => 'completed',
                    'trans_source' => 'API',
                    'performed_by' => $performedBy,
                    'approved_by' => $user->id,
                    'metadata' => [
                        'service' => 'ai_assistant',
                        'field_code' => $serviceField->field_code,
                        'api_response' => 'success'
                    ],
                ]);

                // Debit Wallet
                $wallet->decrement('balance', $servicePrice);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Successful',
                    'data' => [
                        'answer' => $aiResult['choices'][0]['message']['content'] ?? '',
                        'model' => $aiResult['model'] ?? '',
                    ],
                    'transaction_ref' => $transactionRef,
                    'charge' => $servicePrice
                ], 200);
            });

        } catch (\Exception $e) {
            Log::error('AI API Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An internal error occurred.'
            ], 500);
        }
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
        return $token ? User::where('api_token', $token)->first() : null;
    }
}
