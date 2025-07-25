<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageLogs extends Model
{
    use HasFactory;
    protected $table = 'message_logs';
    protected $fillable = [
        'device', 'target', 'message', 'state_id', 'status', 'state',
    ];

}

