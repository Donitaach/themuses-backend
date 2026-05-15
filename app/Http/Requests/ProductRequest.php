<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

// tambah namespace
use Illuminate\Foundation\Http\Attributes\RedirectTo;
use Illuminate\Foundation\Http\Attributes\StopOnFirstFailure;

//tambah attribute
#[RedirectTo('/products')]  //redirect jika gagal validasi 
#[StopOnFirstFailure]       //stop validasi saat error pertama

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;    // ijinkan semua user boleh mengakses request ini
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    return [
    'name' => 'required|string',
    'description' => 'required|string',
    'price' => 'required|numeric',
    'category_id' => 'required|exists:categories,id',
    'stock' => 'required|integer',

    'material' => 'nullable|string',
    'weight' => 'nullable|numeric',
    'gemstone' => 'nullable|string',
    'size' => 'nullable|string',

    'image_url' => 'nullable|image'
];
    }
}
