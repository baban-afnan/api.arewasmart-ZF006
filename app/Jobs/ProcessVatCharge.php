<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessVatCharge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $user = User::find($this->userId);
        if (!$user) {
            Log::warning("ProcessVatCharge: User {$this->userId} not found.");
            return;
        }

        $amount = 15;
        $description = 'VAT value added tax';
        $transactionRef = 'VAT-' . strtoupper(Str::random(10));

        // Debit Wallet
        $wallet = Wallet::where('user_id', $this->userId)->first();
        if ($wallet) {
            $wallet->decrement('balance', $amount);
            Log::info("ProcessVatCharge: Debited {$amount} from user {$this->userId} wallet.");
        } else {
            Log::warning("ProcessVatCharge: Wallet for user {$this->userId} not found.");
        }

        // Create Transaction
        Transaction::create([
            'user_id' => $this->userId,
            'transaction_ref' => $transactionRef,
            'type' => 'debit',
            'amount' => $amount,
            'description' => $description,
            'status' => 'completed',
            'performed_by' => 'System',
            'metadata' => ['type' => 'vat_charge'],
        ]);

        Log::info("Processed VAT charge transaction for user {$this->userId}");
    }
}
