<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\TableSession;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    function index(Request $request) {
        $tableSession = $request->table_session;

        $orders = $tableSession->orders()->with('items.item')->get();

        return OrderResource::collection($orders);
    }

    function store(Request $request) {
        $tableSession = $request->table_session;

        $validated = $request->validate([
            'orders' => 'required|array|min:1',

            'orders.*.item_id' => 'required|integer|exists:items,id',
            'orders.*.amount' => 'required|integer|min:1'
        ]);

        $order = $tableSession->orders()->create();

        $order->items()->createMany($validated['orders']);

        $order->refresh()->load('items.item');

        return new OrderResource($order);
    }
}
