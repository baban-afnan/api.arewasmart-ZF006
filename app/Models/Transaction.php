<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model


{
    protected $table = 'transactions'; 
    
    protected $fillable = [
        'transaction_ref', 
        'payer_name',
        'referenceId',
        'user_id', 
        'amount', 
        'fee',
        'net_amount',
        'description',
        'type', 
        'status', 
        'trans_source',
        'metadata',
        'performed_by',
        'approved_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
