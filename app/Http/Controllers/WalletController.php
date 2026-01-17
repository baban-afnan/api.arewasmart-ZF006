<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VirtualAccount;
use App\Repositories\VirtualAccountRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * Show wallet dashboard
     */
    public function index()
    {
        $userId = Auth::id();
        $user = Auth::user();

        $virtualAccount = VirtualAccount::where('user_id', $userId)->first();
        $wallet = Wallet::where('user_id', $userId)->first();

        // Handle case where wallet might not exist yet
        if (!$wallet) {
            // Ideally create one here or show error. For now, empty structure.
             $walletData = [
                'wallet_balance'    => 0,
                'bonus'             => $user->referral_bonus ?? 0,
                'status'            => 'inactive',
                'available_balance' => 0,
            ];
        } else {
             $walletData = [
                'wallet_balance'    => $wallet->balance, // Maps to 'balance' in DB
                'bonus'             => $user->referral_bonus ?? 0, // Maps to 'referral_bonus' on User
                'status'            => $wallet->status ?? 'active',
                'available_balance' => $wallet->available_balance ?? 0,
            ];
        }

        return view('wallet.fund', compact('virtualAccount', 'walletData'));
    }

    /**
     * Show bonus/available balance page
     */
    public function bonus()
    {
        $userId = Auth::id();
        $user = Auth::user();

        $wallet = Wallet::where('user_id', $userId)->first();

        // Handle case where wallet might not exist yet
        if (!$wallet) {
            $walletData = [
                'wallet_balance'    => 0,
                'bonus'             => $user->referral_bonus ?? 0,
                'status'            => 'inactive',
                'available_balance' => 0,
            ];
        } else {
            $walletData = [
                'wallet_balance'    => $wallet->balance,
                'bonus'             => $user->referral_bonus ?? 0,
                'status'            => $wallet->status ?? 'active',
                'available_balance' => $wallet->available_balance ?? 0,
            ];
        }

        return view('wallet.bonus', compact('walletData'));
    }

    /**
     * Create Virtual Wallet
     */
    public function createWallet(Request $request)
    {
        $loginUserId = Auth::id(); 
        $user = User::find($loginUserId);

        // Check KYC details
        if (empty($user->bvn) || empty($user->phone_no)) {
            return redirect()->route('wallet')->with([
                'error' => 'Please complete your registration by providing your BVN and Phone Number to open a virtual account.'
            ]);
        }

        // Repository call
        $repObj2 = new VirtualAccountRepository;
        $result = $repObj2->createVirtualAccount($loginUserId);

        // Handle failure
        if (!is_array($result) || !isset($result['success']) || !$result['success']) {
            $message = is_array($result) && isset($result['message'])
                ? $result['message']
                : 'Wallet creation failed. Please try again later.';

            return redirect()->route('wallet')->with(['error' => $message]);
        }

        // Success
        return redirect()->route('wallet')->with(['success' => $result['message']]);
    }

    /**
     * Claim bonus: move bonus to wallet_balance and record transaction
     */
    public function claimBonus(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
        $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();

        if (!$wallet) {
             return redirect()->route('wallet')->with(['error' => 'Wallet not found.']);
        }

        if ($user->referral_bonus <= 0) {
            return redirect()->route('wallet')->with(['error' => 'No bonus available to claim.']);
        }

        DB::transaction(function () use ($wallet, $user) {
            $bonusAmount = $user->referral_bonus;

            // Update wallet balances (Using 'balance' column from DB schema)
            $wallet->balance += $bonusAmount;
            $wallet->available_balance += $bonusAmount;
            $wallet->save();

            // Reset Bonus on User
            $user->referral_bonus = 0;
            $user->save();

            // Performed by
            $performedBy = $user->first_name . ' ' . $user->last_name;

            // Save transaction
            Transaction::create([
                'user_id'         => $user->id,
                'type'            => 'credit',
                'amount'          => $bonusAmount,
                'description'     => 'Bonus claimed and credited to wallet balance',
                'status'          => 'completed',
                'transaction_ref' => 'BONUS-' . strtoupper(Str::random(10)),
                'performed_by'    => $performedBy,
            ]);
        });

        return redirect()->route('wallet')->with(['success' => 'Bonus successfully claimed and added to your wallet balance.']);
    }

    /**
     * Transfer available_balance to main wallet balance
     */
    public function transferAvailableBalance(Request $request)
    {
        $request->validate([
            'pin' => 'required|numeric|digits:5',
        ]);

        $userId = Auth::id();
        $user = Auth::user();
        
        // Verify PIN
        if (!\Illuminate\Support\Facades\Hash::check($request->pin, $user->pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid transaction PIN.'
            ], 403);
        }

        try {
            return DB::transaction(function () use ($userId, $user) {
                $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();

                if (!$wallet) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Wallet not found.'
                    ], 404);
                }

                $availableBalance = $wallet->available_balance ?? 0;

                if ($availableBalance <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No available balance to transfer.'
                    ], 400);
                }

                // Transfer available_balance to balance
                $wallet->balance += $availableBalance;
                $wallet->available_balance = 0;
                $wallet->save();

                // Create transaction record using user's preferred format
                $performedBy = $user->first_name . ' ' . $user->last_name;
                $transactionRef = 'Btr1101-' . strtoupper(Str::random(10));

                Transaction::create([
                    'user_id'         => $userId,
                    'payer_name'      => $performedBy,
                    'transaction_ref' => $transactionRef,
                    'type'            => 'credit',
                    'description'     => 'Transfer from Available Balance to Main Wallet',
                    'amount'          => $availableBalance,
                    'status'          => 'completed',
                    'performed_by'    => $performedBy,
                    'trans_source'    => 'api',
                    'metadata'        => json_encode(['source' => 'available_balance']),
                    'created_at'      => \Carbon\Carbon::now(),
                    'updated_at'      => \Carbon\Carbon::now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Transfer successful',
                    'amount' => $availableBalance,
                    'new_balance' => $wallet->balance,
                    'transaction_ref' => $transactionRef
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage()
            ], 500);
        }
    }

}
