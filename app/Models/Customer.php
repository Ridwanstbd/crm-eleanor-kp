<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'last_time_message'];

    // Relasi many-to-many ke grup pelanggan
    public function groups()
    {
        return $this->belongsToMany(CustomerGroup::class, 'customer_customer_group')
                    ->using(CustomerCustomerGroup::class);
    }

    // Relasi many-to-many ke produk yang pernah dibeli
    public function purchases()
    {
        return $this->belongsToMany(Product::class, 'customer_product_purchases')
                    ->withPivot('last_purchase_quantity')
                    ->using(CustomerProductPurchase::class);
    }

    // Relasi satu-ke-banyak ke log pesan
    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }
}
