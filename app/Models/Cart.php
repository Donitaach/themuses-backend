<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // 🔥 field yang bisa diisi
    protected $fillable = [
        'user_id',
    ];

    // =========================
    // 🔗 RELATION
    // =========================

    // 🧑 cart milik 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🛒 cart punya banyak item
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}