<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Product.php
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'default_estimation_days_per_unit'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
    public function purchasedByCustomers()
    {
    return $this->belongsToMany(Customer::class, 'customer_product_purchases')
                ->withPivot('last_purchase_quantity')
                ->using(CustomerProductPurchase::class);
    }
}
