<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderNotifications extends Model
{
    protected $table = 'vw_ordernotifications';
    protected $primaryKey = 'notification_id';
    public $timestamps = true;

    protected $fillable = [
        'notification_id',
        'user_id',
        'vendor_id',
        'order_id',
        'notification_type',
        'notification_type',
        'notification_text',
        'user_status',
        'vendor_status',
        'created_at',
        'updated_at',
    ];
}
