<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\TableSession;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    function index(Request $request) {
        $tableSession = TableSession::with('orders')->where('token', $request->bearerToken())->first();

        if (!$tableSession) {
            return response()->json([
                'message' => 'Table session not found'
            ], 404);
        }

        $orders = $tableSession->orders;

        $orders->load('items.item');

        return OrderResource::collection($orders);
    }

    function store() {
        
    }
}
