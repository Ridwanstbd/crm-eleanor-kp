<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Campaign; 

class MessageLogs extends Model
{
    use HasFactory;
    protected $table = 'message_logs';
    protected $primaryKey = 'report_id'; 
    public $incrementing = false;
    protected $keyType = 'string'; 
    public $timestamps = true;

    protected $fillable = [
        'report_id', 'device', 'target', 'message', 'state_id', 'status', 'state',
        'campaign_id', 'customer_id' 
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}