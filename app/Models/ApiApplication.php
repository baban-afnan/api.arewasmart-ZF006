<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_type',
        'website_link',
        'business_description',
        'business_nature',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
