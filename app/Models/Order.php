<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [

        'user_id',

        'total_price',

        'status',

        'customer_name',

        'phone',

        'address',

        'external_id',

        'invoice_url',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(
            OrderItem::class
        );
    }
}