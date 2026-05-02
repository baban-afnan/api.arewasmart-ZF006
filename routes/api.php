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
    Route::post('/bvn/verify', [BvnVerificationController::class, 'verify'])->middleware('throttle:60,1');
    
    // NIN Verification
    Route::post('/nin/verify', [NinVerificationController::class, 'verify'])->middleware('throttle:60,1');
    Route::post('/nin/demo', [\App\Http\Controllers\Api\NinDemoController::class, 'store']);
    Route::post('/nin/phone', [\App\Http\Controllers\Api\NinPhoneController::class, 'store']);

    // TIN Verification
    Route::post('/tin/verify', [\App\Http\Controllers\Api\TinVerificationController::class, 'verify']);

    // NIN Validation & IPE
    Route::post('/nin/validation', [\App\Http\Controllers\Agency\NinValidationController::class, 'store'])->middleware('throttle:60,1');
    Route::get('/nin/validation', [\App\Http\Controllers\Agency\NinValidationController::class, 'checkStatus'])->middleware('throttle:60,1');

    Route::post('/nin/ipe', [\App\Http\Controllers\Agency\NinIpeController::class, 'store'])->middleware('throttle:60,1');
    Route::get('/nin/ipe', [\App\Http\Controllers\Agency\NinIpeController::class, 'checkStatus'])->middleware('throttle:60,1');

    // NIN Modification
    Route::post('/nin/modification', [\App\Http\Controllers\Agency\NinModificationController::class, 'store'])->middleware('throttle:60,1');
    Route::get('/nin/modification', [\App\Http\Controllers\Agency\NinModificationController::class, 'checkStatus'])->middleware('throttle:60,1');

    // BVN Modification
    Route::post('/bvn/modification', [\App\Http\Controllers\Agency\BvnModificationController::class, 'store'])->name('bvn.modification.store')->middleware('throttle:60,1');
    Route::get('/bvn/modification', [\App\Http\Controllers\Agency\BvnModificationController::class, 'checkStatus'])->name('bvn.modification.status')->middleware('throttle:60,1');

    // BVN CRM
    Route::post('/bvn/crm', [\App\Http\Controllers\Agency\BvnCrmController::class, 'store'])->name('bvn.crm.store')->middleware('throttle:60,1');
    Route::get('/bvn/crm', [\App\Http\Controllers\Agency\BvnCrmController::class, 'checkStatus'])->name('bvn.crm.status')->middleware('throttle:60,1');
    
    // BVN Search
    Route::post('/bvn/phone-search', [\App\Http\Controllers\Agency\BvnPhoneSearchController::class, 'store'])->name('bvn.phone_search.store')->middleware('throttle:60,1');
    Route::get('/bvn/phone-search', [\App\Http\Controllers\Agency\BvnPhoneSearchController::class, 'checkStatus'])->name('bvn.phone_search.status')->middleware('throttle:60,1');


    // Airtime Purchase
    Route::post('/airtime/purchase', [\App\Http\Controllers\Billpayment\AirtimeController::class, 'purchase'])->name('api.airtime.purchase');

    // Data API (Variations & Purchase)
    Route::get('/data/variations', [\App\Http\Controllers\Billpayment\DataController::class, 'getVariations'])->name('api.data.variations');
    Route::post('/data/purchase', [\App\Http\Controllers\Billpayment\DataController::class, 'purchase'])->name('api.data.purchase');

    // Electricity API
    Route::get('/electricity/variations', [\App\Http\Controllers\Billpayment\ElectricityController::class, 'getVariations'])->name('api.electricity.variations');
    Route::post('/electricity/verify', [\App\Http\Controllers\Billpayment\ElectricityController::class, 'verifyMeter'])->name('api.electricity.verify');
    Route::post('/electricity/purchase', [\App\Http\Controllers\Billpayment\ElectricityController::class, 'purchase'])->name('api.electricity.purchase');

    // SME Data API
    Route::get('/sme-data/variations', [\App\Http\Controllers\Billpayment\SmeDataController::class, 'getVariations'])->name('api.sme-data.variations');
    Route::post('/sme-data/purchase', [\App\Http\Controllers\Billpayment\SmeDataController::class, 'purchase'])->name('api.sme-data.purchase');

    // TV API
    Route::get('/tv/variations', [\App\Http\Controllers\Billpayment\CableController::class, 'getVariations'])->middleware('throttle:60,1')->name('api.tv.variations');
    Route::post('/tv/verify', [\App\Http\Controllers\Billpayment\CableController::class, 'verifyIuc'])->middleware('throttle:60,1')->name('api.tv.verify');
    Route::post('/tv/purchase', [\App\Http\Controllers\Billpayment\CableController::class, 'purchase'])->middleware('throttle:60,1')->name('api.tv.purchase');

    // Education API
    Route::get('/education/variations', [\App\Http\Controllers\Billpayment\EducationController::class, 'getVariations'])->middleware('throttle:60,1')->name('api.education.variations');
    Route::post('/education/verify', [\App\Http\Controllers\Billpayment\EducationController::class, 'verifyProfile'])->middleware('throttle:60,1')->name('api.education.verify');
    Route::post('/education/purchase', [\App\Http\Controllers\Billpayment\EducationController::class, 'purchase'])->middleware('throttle:60,1')->name('api.education.purchase');

    // AI API
    Route::post('/ai/chat', [\App\Http\Controllers\Api\AiApiController::class, 'chat'])->middleware('throttle:50,1')->name('api.ai.chat');
});

// Webhooks
Route::post('/nin/webhook', [\App\Http\Controllers\Agency\NinValidationController::class, 'webhook']);