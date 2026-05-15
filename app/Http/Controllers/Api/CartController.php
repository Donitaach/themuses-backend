<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Mengambil data keranjang milik user
     */
    public function index()
    {
        // Ganti dengan auth()->id() jika sudah menggunakan sistem login
        $userId = 1; 

        return Cart::where('user_id', $userId)
            ->with('items.product')
            ->first()
            ?? ['items' => []];
    }

    /**
     * Menambah produk ke dalam keranjang
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);

        // 1. Cek jika stok produk sudah habis total
        if ($product->stock <= 0) {
            return response()->json([
                'message' => 'Maaf, stok produk ini sudah habis.'
            ], 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => 1]);

        $item = CartItem::where([
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ])->first();

        if ($item) {
            // 2. Cek jika penambahan quantity akan melampaui stok
            if (($item->quantity + 1) > $product->stock) {
                return response()->json([
                    'message' => "Tidak bisa menambah barang. Stok tersedia hanya {$product->stock} unit."
                ], 400);
            }
            $item->increment('quantity');
        } else {
            // Jika item baru, quantity default adalah 1
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke koleksi.'
        ]);
    }

    /**
     * Memperbarui jumlah barang di keranjang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $item = CartItem::with('product')->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        // 3. Validasi stok saat update quantity (misal input manual)
        if ($request->quantity > $item->product->stock) {
            return response()->json([
                'message' => "Jumlah melampaui stok. Hanya tersedia {$item->product->stock} unit."
            ], 400);
        }

        $item->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'message' => 'Jumlah berhasil diperbarui.'
        ]);
    }

    /**
     * Menghapus satu item dari keranjang
     */
    public function destroy($id)
    {
        $item = CartItem::find($id);

        if ($item) {
            $item->delete();
            return response()->json(['message' => 'Item telah dihapus dari koleksi.']);
        }

        return response()->json(['message' => 'Item tidak ditemukan'], 404);
    }

    /**
     * Mengosongkan seluruh isi keranjang
     */
    public function clear()
    {
        $userId = 1;
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'message' => 'Keranjang berhasil dikosongkan.'
        ]);
    }
}