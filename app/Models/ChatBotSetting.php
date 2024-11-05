<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatBotSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'url',
        'version_id',
    ];
}
