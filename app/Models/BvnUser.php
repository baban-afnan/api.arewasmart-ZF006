<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BvnUser extends Model
{
    use HasFactory;

    protected $table = 'bvn_user';

    protected $fillable = [
        'reference',
        'user_id',
        'service_field_id',
        'service_id',
        'bvn',
        'agent_location',
        'bank_name',
        'account_no',
        'account_name',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone_no',
        'address',
        'state',
        'lga',
        'dob',
        'status',
        'transaction_id',
        'submission_date',
        'comment',
        'query',
        'performed_by',
        'approved_by',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceField()
    {
        return $this->belongsTo(ServiceField::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
