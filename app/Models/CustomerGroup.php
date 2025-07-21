<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/CustomerGroup.php
class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

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

