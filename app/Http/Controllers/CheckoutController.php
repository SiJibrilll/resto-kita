<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    function checkout(string $token, Request $request) {
        $tableSession = $request->table_session;
        
        $tableSession->load('orders.items.item');

        $invoice = DB::transaction(function () use ($tableSession) {
            $subtotal = $tableSession->orders
                ->flatMap->items
                ->sum->subtotal;
            
            $invoice = $tableSession->invoice()->create([
                'grand_total' => $subtotal
            ]);

            $tableSession->update([
                'status' => 'checked_out'
            ]);

            return $invoice;
        });


        $invoice->refresh()->load('table_session.orders.items.item');

        return new InvoiceResource($invoice);
    }
}
