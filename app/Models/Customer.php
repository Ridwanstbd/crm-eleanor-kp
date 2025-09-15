<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'last_time_message'];

    public function groups()
    {
        return $this->belongsToMany(CustomerGroup::class, 'customer_customer_group')
                    ->using(CustomerCustomerGroup::class);
    }

    public function purchases()
    {
        return $this->belongsToMany(Product::class, 'customer_product_purchases')
                    ->withPivot([
                        'campaign_id',
                        'last_purchase_quantity',
                        'receipt',
                        'schedule',
                        'time_send'
                    ])
                    ->using(CustomerProductPurchase::class);
    }

    public function messageLogs()
    {
        return $this->hasMany(MessageLogs::class);
    }

    public function campaigns()
    {
        return $this->belongsToManyThrough(
            Campaign::class,
            CustomerGroup::class,
            'customer_customer_group',
            'campaign_customer_group',
            'id',
            'id',
            'customer_group_id',
            'customer_group_id'
        );
    }
}
