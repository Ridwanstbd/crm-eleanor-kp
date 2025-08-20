<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

// app/Models/CampaignCustomerGroup.php
class CampaignCustomerGroup extends Pivot
{

    protected $table = 'campaign_customer_group';

    protected $fillable = [
        'campaign_id', 'customer_group_id'
    ];

    public $timestamps = false;

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
