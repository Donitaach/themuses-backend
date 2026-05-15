<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | DEBUG LOG
        |--------------------------------------------------------------------------
        */

        \Log::info('XENDIT WEBHOOK');
        \Log::info($request->all());

        /*
        |--------------------------------------------------------------------------
        | VALIDASI TOKEN
        |--------------------------------------------------------------------------
        */

        $callbackToken =
            $request->header(
                'x-callback-token'
            );

        \Log::info([
            'TOKEN_HEADER' =>
                $callbackToken,

            'TOKEN_ENV' =>
                config(
                    'services.xendit.webhook_token'
                )
        ]);

        /*
        |--------------------------------------------------------------------------
        | TOKEN INVALID
        |--------------------------------------------------------------------------
        */

        if (
            $callbackToken !==
            config(
                'services.xendit.webhook_token'
            )
        ) {

            \Log::error(
                'TOKEN INVALID'
            );

            return response()->json([

                'message' =>
                    'Invalid token'

            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA
        |--------------------------------------------------------------------------
        */

        $externalId =
            $request->external_id;

        $status =
            $request->status;

        /*
        |--------------------------------------------------------------------------
        | CARI ORDER
        |--------------------------------------------------------------------------
        */

        $order = Order::where(

            'external_id',
            $externalId

        )->first();

        /*
        |--------------------------------------------------------------------------
        | ORDER TIDAK ADA
        |--------------------------------------------------------------------------
        */

        if (!$order) {

            \Log::error(
                'ORDER NOT FOUND'
            );

            return response()->json([

                'message' =>
                    'Order not found'

            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE ORDER
        |--------------------------------------------------------------------------
        */

        $order->update([

            'status' =>
                strtoupper($status),

            'payment_method' =>
                $request->payment_method,

            'paid_at' =>

                strtoupper($status) ===
                'PAID'

                    ? now()

                    : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        \Log::info(
            'ORDER UPDATED'
        );

        return response()->json([

            'success' => true
        ]);
    }
}