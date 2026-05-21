<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS IMPORT
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\XenditWebhookController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| Rute-rute di bawah ini dapat diakses oleh siapa saja tanpa perlu 
| melampirkan token login (Bearer Token).
|
*/

// Melihat daftar produk dan detail produk
Route::apiResource('products', ProductController::class);

// Autentikasi pendaftaran akun baru & masuk log
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| XENDIT WEBHOOK (WAJIB PUBLIC)
|--------------------------------------------------------------------------
| Jangan pernah memasukkan rute ini ke dalam middleware auth:sanctum 
| karena server Xendit tidak memiliki akses token akun user kita.
|
*/
Route::post('/xendit/webhook', [XenditWebhookController::class, 'handle']);


/*
|--------------------------------------------------------------------------
| PRIVATE ROUTES (Membentengi Akses Menggunakan Sanctum)
|--------------------------------------------------------------------------
| Seluruh rute di dalam grup ini membutuhkan header:
| Authorization: Bearer <token_kamu>
| Jika tidak menyertakannya, Laravel akan otomatis menolak request (401).
|
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | CART ROUTES (Sistem Keranjang Per-User)
    |--------------------------------------------------------------------------
    */
    Route::prefix('cart')->group(function () {
        
        // Mengambil data keranjang milik user yang sedang aktif login
        Route::get('/', [CartController::class, 'index']);
        
        // Menambahkan produk ke dalam keranjang belanja
        Route::post('/', [CartController::class, 'store']);
        
        // Mengosongkan seluruh isi keranjang belanja
        Route::delete('/clear', [CartController::class, 'clear']);
        
        // Memperbarui jumlah item (quantity) tertentu di keranjang
        Route::put('/{id}', [CartController::class, 'update']);
        
        // Menghapus satu item produk dari keranjang belanja
        Route::delete('/{id}', [CartController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | USER PROFILE ROUTES
    |--------------------------------------------------------------------------
    */
    // Mengambil data informasi profil user aktif
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // Memperbarui informasi data profil user aktif
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // Menghapus sesi login (token) dari database backend
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | TRANSACTIONS & ORDERS ROUTES
    |--------------------------------------------------------------------------
    */
    // Membuat transaksi pembayaran baru (Checkout)
    Route::post('/checkout', [CheckoutController::class, 'store']);
    
    // Mengambil riwayat pembelian (Order History) milik user aktif
    Route::get('/orders', [OrderController::class, 'index']);
});