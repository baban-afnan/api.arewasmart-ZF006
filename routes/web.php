<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureApiUser::class, \App\Http\Middleware\TwoFactorMiddleware::class])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Force Profile Update (KYC)
    Route::post('/profile/update-required', [ProfileController::class, 'updateRequired'])->name('profile.updateRequired');

    // Profile Settings
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.pin');
    Route::post('/profile/api-token/regenerate', [ProfileController::class, 'regenerateApiToken'])->name('profile.api-token.regenerate');
    
    // 2FA Routes
    Route::get('verify/resend', [App\Http\Controllers\Auth\TwoFactorController::class, 'resend'])->name('verify.resend');
    Route::resource('verify', App\Http\Controllers\Auth\TwoFactorController::class)->only(['index', 'store']);
    Route::post('profile/two-factor', [App\Http\Controllers\Auth\TwoFactorController::class, 'toggle'])->name('profile.two-factor.toggle');
    
    // API Application
    Route::post('/api-application', [App\Http\Controllers\ApiApplicationController::class, 'store'])->name('api.application.store');

    // Wallet Funding
    Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet');
    Route::get('/wallet/bonus', [App\Http\Controllers\WalletController::class, 'bonus'])->name('wallet.bonus');
    Route::post('/wallet/claim-bonus', [App\Http\Controllers\WalletController::class, 'claimBonus'])->name('wallet.claimBonus');
    Route::post('/wallet/create-virtual-account', [App\Http\Controllers\WalletController::class, 'createWallet'])->name('wallet.create');
    Route::post('/wallet/transfer-available', [App\Http\Controllers\WalletController::class, 'transferAvailableBalance'])->name('wallet.transfer');

    // Transactions
    Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    // NIN Verification
    Route::get('/verification/nin', [App\Http\Controllers\Api\NinVerificationController::class, 'index'])->name('nin.verification.index');
    Route::post('/verification/nin', [App\Http\Controllers\Api\NinVerificationController::class, 'verify'])->name('nin.verification.store');
});

// API Routes are now in routes/api.php

// Developer Documentation
Route::group(['prefix' => 'developer', 'as' => 'developer.', 'middleware' => ['auth']], function () {
    Route::get('/bvn', [App\Http\Controllers\Api\BvnVerificationController::class, 'index'])->name('bvn.docs');

    Route::get('/nin', [App\Http\Controllers\Api\NinVerificationController::class, 'index'])->name('nin.docs');

    Route::get('/tin', [\App\Http\Controllers\Api\TinVerificationController::class, 'index'])->name('tin.docs');
    
    Route::get('/nin-validation', [\App\Http\Controllers\Agency\NinValidationController::class, 'index'])->name('nin.validation.docs');
    Route::get('/nin-ipe', [\App\Http\Controllers\Agency\NinIpeController::class, 'index'])->name('nin.ipe.docs');

    Route::get('/nin-modification', [\App\Http\Controllers\Agency\NinModificationController::class, 'index'])->name('nin.modification.docs');
    
    Route::get('/bvn-modification', [\App\Http\Controllers\Agency\BvnModificationController::class, 'index'])->name('bvn.modification.docs');

    Route::get('/bvn-modification/fields/{serviceId}', [\App\Http\Controllers\Agency\BvnModificationController::class, 'getServiceFields'])->name('bvn.modification.fields');

    // Airtime API Docs
    Route::get('/airtime', [\App\Http\Controllers\Billpayment\AirtimeController::class, 'index'])->name('airtime.docs');

    // Data API Docs
    Route::get('/data', [\App\Http\Controllers\Billpayment\DataController::class, 'index'])->name('data.docs');

    // Electricity API Docs
    Route::get('/electricity', [\App\Http\Controllers\Billpayment\ElectricityController::class, 'index'])->name('electricity.docs');

    // NIN Demo
    Route::get('/nin-demo', [\App\Http\Controllers\Api\NinDemoController::class, 'index'])->name('nin.demo.docs');

    // NIN Phone
    Route::get('/nin-phone', [\App\Http\Controllers\Api\NinPhoneController::class, 'index'])->name('nin.phone.docs');


});

// Authenticated API Routes (Web-based auth for docs/usage or just API endpoints)
// Ideally API endpoints should be in routes/api.php but based on 'EnsureApiUser' usage in web.php for other verification routes:


require __DIR__.'/auth.php';
