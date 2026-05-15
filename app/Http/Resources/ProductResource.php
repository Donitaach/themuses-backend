<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'stock' => $this->stock,

            // 🔥 field perhiasan
            'material' => $this->material,
            'weight' => $this->weight,
            'gemstone' => $this->gemstone,
            'size' => $this->size,

            // 🖼️ gambar
            'image_url' => $this->image_url 
                ? asset('storage/' . $this->image_url) 
                : null,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}