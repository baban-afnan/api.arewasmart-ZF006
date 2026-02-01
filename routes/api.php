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
    Route::post('/nin/demo', [\App\Http\Controllers\Api\NinDemoController::class, 'store']);
    Route::post('/nin/phone', [\App\Http\Controllers\Api\NinPhoneController::class, 'store']);

    // TIN Verification
    Route::post('/tin/verify', [\App\Http\Controllers\Api\TinVerificationController::class, 'verify']);

    // NIN Validation & IPE
    Route::post('/nin/validation', [\App\Http\Controllers\Agency\NinValidationController::class, 'store']);
    Route::get('/nin/validation', [\App\Http\Controllers\Agency\NinValidationController::class, 'checkStatus']);

    Route::post('/nin/ipe', [\App\Http\Controllers\Agency\NinIpeController::class, 'store']);
    Route::get('/nin/ipe', [\App\Http\Controllers\Agency\NinIpeController::class, 'checkStatus']);

    // NIN Modification
    Route::post('/nin/modification', [\App\Http\Controllers\Agency\NinModificationController::class, 'store']);
    Route::get('/nin/modification', [\App\Http\Controllers\Agency\NinModificationController::class, 'checkStatus']);

    // BVN Modification
    Route::post('/bvn/modification', [\App\Http\Controllers\Agency\BvnModificationController::class, 'store'])->name('bvn.modification.store');
    Route::get('/bvn/modification', [\App\Http\Controllers\Agency\BvnModificationController::class, 'checkStatus'])->name('bvn.modification.status');


    // Airtime Purchase
    Route::post('/airtime/purchase', [\App\Http\Controllers\Billpayment\AirtimeController::class, 'purchase'])->name('api.airtime.purchase');

    // Data API (Variations & Purchase)
    Route::get('/data/variations', [\App\Http\Controllers\Billpayment\DataController::class, 'getVariations'])->name('api.data.variations');
    Route::post('/data/purchase', [\App\Http\Controllers\Billpayment\DataController::class, 'purchase'])->name('api.data.purchase');

    // Electricity API
    Route::get('/electricity/variations', [\App\Http\Controllers\Billpayment\ElectricityController::class, 'getVariations'])->name('api.electricity.variations');
    Route::post('/electricity/verify', [\App\Http\Controllers\Billpayment\ElectricityController::class, 'verifyMeter'])->name('api.electricity.verify');
    Route::post('/electricity/purchase', [\App\Http\Controllers\Billpayment\ElectricityController::class, 'purchase'])->name('api.electricity.purchase');
});

// Webhooks
Route::post('/nin/webhook', [\App\Http\Controllers\Agency\NinValidationController::class, 'webhook']);