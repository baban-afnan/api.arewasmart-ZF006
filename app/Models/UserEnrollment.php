<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'bvn',
        'agt_mgt_inst_name',
        'agt_mgt_inst_code',
        'agent_name',
        'agent_code',
        'enroller_code',
        'latitude',
        'longitude',
        'finger_print_scanner',
        'bms_import_id',
        'validation_status',
        'validation_message',
        'amount',
        'capture_date',
        'sync_date',
        'validation_date',
    ];
}
