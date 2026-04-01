<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
class PaymentController extends Controller
{

    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    function createTransaction(Request $request) {
        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'customer_name' => ['required', 'string']
        ]);

        $invoice = Invoice::with('payment')->find($validated['invoice_id']);

        if ($invoice->payment) {
            return response()->json([
                'data' => [
                    'snap_token' => $invoice->payment->snap_token,
                    'client_key' => config('midtrans.client_key'),
                    'from_cache'  => true, // optional, useful for debugging
                ]
            ]);
        }

        // No token yet — fetch one from Midtrans
        $params = [
            'transaction_details' => [
                'order_id'     => $invoice->id,
                'gross_amount' => $invoice->grand_total,
            ],
            'customer_details' => [
                'first_name' => $validated['customer_name'],
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            // Persist the token so we never need to fetch it again
            $invoice->payment()->create([
                'snap_token' => $snapToken,
                'customer_name' => $validated['customer_name']
            ]);

            return response()->json([
                'data' => [
                    'snap_token' => $snapToken,
                    'client_key' => config('midtrans.client_key'),
                    'from_cache'  => false,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
