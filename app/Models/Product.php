<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable([
    'category_id',
    'name',
    'description',
    'price',
    'stock',
    'image_url',
    'material',
    'weight',
    'gemstone',
    'size'
])]

class Product extends Model
{
  public function category()
    {
        // Jika Category.php sudah ada di folder yang sama, ini akan otomatis terbaca
        return $this->belongsTo(Category::class);
    }
}
