<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function handleWebhook(Request $request)
    {
        $notif = $request->all();

        // Verify signature key
        $signatureKey = hash('sha512',
            $notif['order_id'] .
            $notif['status_code'] .
            $notif['gross_amount'] .
            config('midtrans.server_key')
        );

        if ($signatureKey !== $notif['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $invoice_id           = $notif['order_id'];
        $transactionStatus = $notif['transaction_status'];
        $fraudStatus       = $notif['fraud_status'] ?? null;
        $paymentMethod = $notif['payment_tyoe'];

        // Map Midtrans statuses to your app's logic
        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            // Payment success - credit card
            $status = 'paid';
        } elseif ($transactionStatus === 'settlement') {
            // Payment success - bank transfer / e-wallet
            $status = 'paid';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $status = 'failed';
        } else {
            $status = 'pending';
        }

        DB::beginTransaction();

        $invoice = Invoice::with('payment')->find($invoice_id);
        $invoice->status = $status;
        $invoice->update();

        $payment = $invoice->payment;

        $payment->status = $status;
        $payment->payment_method = $paymentMethod;
        $payment->paid_at = $status === 'paid' ? now() : null;

        $payment->update();

        DB::commit();

        return response()->json(['message' => 'OK']);
    }

    public function status(Request $request, $invoiceId)
    {
        $payment = Payment::where('invoice_id', $invoiceId)
            ->select('status', 'payment_method', 'paid_at')
            ->firstOrFail();

        return response()->json([
            'data' => $payment
        ]);
    }
}
