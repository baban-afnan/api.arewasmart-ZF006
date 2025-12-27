<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // API Role Check
    Route::middleware(function ($request, $next) {
        if ($request->user()->role !== 'api') {
            \Illuminate\Support\Facades\Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'Access denied. Only API users can access this dashboard.']);
        }
        return $next($request);
    })->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        // Force Profile Update (KYC)
        Route::post('/profile/update-required', [ProfileController::class, 'updateRequired'])->name('profile.updateRequired');

        // Profile Settings
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.pin');
        
        // API Application
        Route::post('/api-application', [App\Http\Controllers\ApiApplicationController::class, 'store'])->name('api.application.store');

        // Wallet Funding
        Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet');
        Route::post('/wallet/claim-bonus', [App\Http\Controllers\WalletController::class, 'claimBonus'])->name('wallet.claimBonus');
        Route::post('/wallet/create-virtual-account', [App\Http\Controllers\WalletController::class, 'createWallet'])->name('wallet.create');

        // Transactions
        Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    });
});

require __DIR__.'/auth.php';
