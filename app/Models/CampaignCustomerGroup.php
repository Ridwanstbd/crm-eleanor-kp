<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/CampaignCustomerGroup.php
class CampaignCustomerGroup extends Pivot
{
    use HasFactory;

    protected $table = 'campaign_customer_group';

    protected $fillable = [
        'campaign_id', 'customer_group_id'
    ];

    public $timestamps = false;
}
