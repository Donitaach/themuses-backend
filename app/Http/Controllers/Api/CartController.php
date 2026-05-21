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
     * Mengambil data keranjang milik user yang sedang login
     */
    public function index()
    {
        // Mengambil ID user secara dinamis dari token Bearer yang dikirim Vue
        $userId = auth()->id(); 

        return Cart::where('user_id', $userId)
            ->with('items.product')
            ->first()
            ?? ['items' => []];
    }

    /**
     * Menambah produk ke dalam keranjang user yang sedang login
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return response()->json([
                'message' => 'Maaf, stok produk ini sudah habis.'
            ], 400);
        }

        // Membuat atau mencari cart berdasarkan ID user yang sedang login
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $item = CartItem::where([
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ])->first();

        if ($item) {
            if (($item->quantity + 1) > $product->stock) {
                return response()->json([
                    'message' => "Tidak bisa menambah barang. Stok tersedia hanya {$product->stock} unit."
                ], 400);
            }
            $item->increment('quantity');
        } else {
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
     * Mengosongkan seluruh isi keranjang milik user yang aktif
     */
    public function clear()
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'message' => 'Keranjang berhasil dikosongkan.'
        ]);
    }
}