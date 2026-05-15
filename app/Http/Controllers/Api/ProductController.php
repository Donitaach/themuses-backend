<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * LIST DATA
     */
    public function index(Request $request)
    {
        $products = Product::latest()
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->paginate(10)
            ->withQueryString();

        return new ProductCollection($products);
    }

    /**
     * SIMPAN DATA
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        // upload gambar
        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')
                ->store('products', 'public');
        }

        $product = Product::create($data);

        return (new ProductResource($product))
            ->additional([
                'status' => true,
                'message' => 'Produk berhasil ditambahkan',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * DETAIL DATA
     */
    public function show(Product $product)
    {
        return (new ProductResource($product))
            ->additional([
                'status' => true,
                'message' => 'Data produk berhasil ditampilkan',
            ]);
    }

    /**
     * UPDATE DATA
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        // upload gambar baru
        if ($request->hasFile('image_url')) {

            // hapus gambar lama
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }

            // simpan gambar baru
            $data['image_url'] = $request->file('image_url')
                ->store('products', 'public');
        }

        $product->update($data);

        return (new ProductResource($product))
            ->additional([
                'status' => true,
                'message' => 'Produk berhasil diupdate',
            ]);
    }

    /**
     * HAPUS DATA
     */
    public function destroy(Product $product)
    {
        // hapus gambar
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil dihapus',
        ]);
    }
}