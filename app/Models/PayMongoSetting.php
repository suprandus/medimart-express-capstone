<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMongoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'paymongo_status',
        'live_mode',
        'public_key',
        'secret_key',
    ];
}
