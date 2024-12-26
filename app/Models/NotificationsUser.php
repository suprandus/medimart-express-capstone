<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationsUser extends Model
{
    protected $table = 'notifications_user';
    protected $primaryKey = 'order_id';
    public $timestamps = true;
    protected $fillable = [
        'notification_id',
        'user_id',
        'user_role',
        'order_id',
        'type',
        'text',
        'status',
        'created_at',
        'updated_at',
    ];
}
