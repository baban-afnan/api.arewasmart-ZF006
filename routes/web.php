<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureApiUser::class])->group(function () {
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
    
    // API Application
    Route::post('/api-application', [App\Http\Controllers\ApiApplicationController::class, 'store'])->name('api.application.store');

    // Wallet Funding
    Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/claim-bonus', [App\Http\Controllers\WalletController::class, 'claimBonus'])->name('wallet.claimBonus');
    Route::post('/wallet/create-virtual-account', [App\Http\Controllers\WalletController::class, 'createWallet'])->name('wallet.create');

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
});

require __DIR__.'/auth.php';
