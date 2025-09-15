<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomerProductPurchase extends Pivot
{
    protected $table = 'customer_product_purchases';

    protected $fillable = [
        'customer_id',
        'campaign_id', 
        'product_id', 
        'last_purchase_quantity',
        'receipt',
        'schedule',
        'time_send'
    ];

    public $timestamps = true; 
    protected $casts = [
        'schedule' => 'date',
        'time_send' => 'datetime:H:i'
    ];
}