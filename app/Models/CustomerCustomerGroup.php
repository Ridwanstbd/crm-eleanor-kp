<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

// app/Models/CustomerCustomerGroup.php
class CustomerCustomerGroup extends Pivot
{

    protected $table = 'customer_customer_group';

    protected $fillable = [
        'customer_id', 'customer_group_id'
    ];

    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
