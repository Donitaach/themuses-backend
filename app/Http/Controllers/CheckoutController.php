<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'items' => 'required|array',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required|email',
        ]);

        DB::beginTransaction();

        try {
            // 2. Konfigurasi Xendit
            Configuration::setXenditKey(config('services.xendit.secret_key'));

            $subtotal = 0;
            $itemsToProcess = [];

            // 3. Validasi Stok & Hitung Total
            foreach ($request->items as $item) {
                // LockForUpdate mencegah orang lain membeli barang yang sama dalam detik yang sama
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'message' => "Stok produk '{$product->name}' tidak mencukupi (Tersisa: {$product->stock})."
                    ], 422);
                }

                // --- PENGURANGAN STOK DI DATABASE ---
                $product->decrement('stock', $item['quantity']);

                $itemPrice = $product->price;
                $itemSubtotal = $itemPrice * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal
                ];
            }

            $shipping = 50000; // Flat ongkir
            $grandTotal = $subtotal + $shipping;

            // 4. Create Order
            $order = Order::create([
                'user_id'       => auth()->id(),
                'total_price'   => $grandTotal,
                'status'        => 'PENDING',
                'customer_name' => $request->first_name . ' ' . $request->last_name,
                'phone'         => $request->phone,
                'address'       => $request->address,
            ]);

            // 5. Create Order Items
            foreach ($itemsToProcess as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);
            }

            // 6. Request Invoice ke Xendit
            $apiInstance = new InvoiceApi();
            $invoiceRequest = [
                'external_id'          => 'ORDER-' . $order->id,
                'description'          => 'Pembayaran Pesanan Perhiasan The Muses #' . $order->id,
                'amount'               => $grandTotal,
                'payer_email'          => $request->email,
                'customer' => [
                    'given_names' => $request->first_name,
                    'surname'     => $request->last_name,
                    'mobile_number' => $request->phone,
                ],
                'success_redirect_url' => 'http://localhost:5173/payment-success',
                'failure_redirect_url' => 'http://localhost:5173/payment-failed',
                'currency'             => 'IDR',
                'reminder_time'        => 1,
            ];

            $invoice = $apiInstance->createInvoice($invoiceRequest);

            // 7. Update Order dengan Link Pembayaran
            $order->update([
                'external_id' => $invoice['external_id'],
                'invoice_url' => $invoice['invoice_url'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'invoice_url' => $invoice['invoice_url']
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}