<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = [
        'name', 
        'total_customers', 
        'processed_customers_data'
    ];

    protected $casts = [
        'processed_customers_data' => 'array',
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_customer_group')
                ->using(CustomerCustomerGroup::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_customer_group')
                ->using(CampaignCustomerGroup::class);
    }
}