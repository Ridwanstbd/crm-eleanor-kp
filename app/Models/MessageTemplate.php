<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/MessageTemplate.php
class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'content'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}

