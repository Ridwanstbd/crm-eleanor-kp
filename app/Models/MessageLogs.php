<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageLogs extends Model
{
    use HasFactory;
    protected $table = 'message_logs';
    protected $primaryKey = 'report_id';
    public $timestamps = true;

    protected $fillable = [
        'device', 'target', 'message', 'state_id', 'status', 'state',
        'sent_at', 'customer_id', 'follow_up_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'follow_up_id');
    }
}

