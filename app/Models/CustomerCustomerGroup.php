<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/CustomerCustomerGroup.php
class CustomerCustomerGroup extends Pivot
{
    use HasFactory;

    protected $table = 'customer_customer_group';

    protected $fillable = [
        'customer_id', 'customer_group_id'
    ];

    public $timestamps = false;
}
