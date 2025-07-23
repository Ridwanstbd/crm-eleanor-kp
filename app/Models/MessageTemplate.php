<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  // Tambahkan ini
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory; // Pastikan trait HasFactory digunakan

    protected $fillable = ['name', 'content'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
