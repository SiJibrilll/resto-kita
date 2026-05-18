<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class CheckoutController extends Controller
{
    function checkout(string $token, Request $request) {
        $tableSession = $request->table_session;

        $tableSession->load('orders.items.item');

        $invoice = DB::transaction(function () use ($tableSession) {
            $subtotal = $tableSession->orders
                ->flatMap->items
                ->sum->subtotal;

            $tax = round($subtotal * 0.10, 2);
            $grandTotal = round($subtotal + $tax, 2);

            $invoice = $tableSession->invoice()->create([
                'grand_total' => $grandTotal
            ]);

            $tableSession->update([
                'status' => 'checked_out',
                'checked_out_at' => now()
            ]);

            return $invoice;
        });

        $invoice->refresh()->load('table_session.orders.items.item');

        return new InvoiceResource($invoice);
    }
}
