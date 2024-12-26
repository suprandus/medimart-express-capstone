<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';
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
