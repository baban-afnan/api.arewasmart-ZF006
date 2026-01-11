<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BvnVerificationController;
use App\Http\Controllers\Api\NinVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// V1 API Routes
Route::prefix('v1')->group(function () {
    // BVN Verification
    Route::post('/bvn/verify', [BvnVerificationController::class, 'verify']);
    
    // NIN Verification
    Route::post('/nin/verify', [NinVerificationController::class, 'verify']);

    // TIN Verification
    Route::post('/tin/verify', [\App\Http\Controllers\Api\TinVerificationController::class, 'verify']);

    // NIN Validation & IPE Status Check
    Route::post('/nin/validation', [\App\Http\Controllers\Agency\NinValidationController::class, 'store']);
    Route::get('/nin/validation', [\App\Http\Controllers\Agency\NinValidationController::class, 'checkStatus']);

    // NIN Modification
    Route::post('/nin/modification', [\App\Http\Controllers\Agency\NinModificationController::class, 'store']);
    Route::get('/nin/modification', [\App\Http\Controllers\Agency\NinModificationController::class, 'checkStatus']);
});

// Webhooks
Route::post('/nin/webhook', [\App\Http\Controllers\Agency\NinValidationController::class, 'webhook']);