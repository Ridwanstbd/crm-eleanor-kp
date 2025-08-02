<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Campaign.php
class Campaign extends Model
{


    protected $fillable = ['user_id', 'message_template_id', 'product_id', 'name', 'schedule','time_send'];

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
}
