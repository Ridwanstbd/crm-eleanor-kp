<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/CustomerProductPurchase.php
class CustomerProductPurchase extends Pivot
{
    use HasFactory;

    protected $table = 'customer_product_purchases';

    protected $fillable = [
        'customer_id', 'product_id', 'last_purchase_quantity'
    ];

    public $timestamps = false;
}

