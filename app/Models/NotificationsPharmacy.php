<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationsPharmacy extends Model
{
    protected $table = 'notifications_pharmacy';
    protected $primaryKey = 'notification_id';
    public $timestamps = true;
    protected $fillable = [
        'notification_id',
        'vendor_id',
        'user_role',
        'order_id',
        'type',
        'text',
        'status',
        'created_at',
        'updated_at',
    ];
}
