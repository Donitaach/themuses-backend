<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // 🔥 field yang boleh diisi
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    // =========================
    // 🔗 RELATION
    // =========================

    // 🛒 item milik 1 cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // 📦 item punya 1 product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}