<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  // Tambahkan ini
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory; 
    protected $fillable = ['name', 'content'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function isUsed()
    {
        return $this->campaigns()->exists();
    }

    public function getCampaignCountAttribute()
    {
        return $this->campaigns()->count();
    }
}
