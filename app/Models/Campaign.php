<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Campaign.php
class Campaign extends Model
{
    protected $fillable = ['user_id', 'message_template_id', 'product_id', 'name','scheduled_at'];

    public function messageLogs()
    {
        return $this->hasMany(MessageLogs::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messageTemplate()
    {
        return $this->belongsTo(MessageTemplate::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customerGroups()
    {
        return $this->belongsToMany(CustomerGroup::class, 'campaign_customer_group')
                ->using(CampaignCustomerGroup::class);
    }

    public function customers()
    {
        return $this->belongsToManyThrough(
            Customer::class,
            CustomerGroup::class,
            'campaign_customer_group',
            'customer_customer_group',
            'id',
            'id',
            'customer_group_id',
            'customer_group_id'
        );
    }

    public function hasProduct()
    {
        return !is_null($this->product_id);
    }

    public function getProductNameAttribute()
    {
        return $this->product ? $this->product->name : 'No Product';
    }
}