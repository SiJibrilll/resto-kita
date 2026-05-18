<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DahsboardController extends Controller
{
    function index(Request $request) {
        $todaysOrder = Order::whereDate('created_at', today())->count();

        $todaysIncome = Payment::whereDate('created_at', today())
        ->where('status', 'paid')
        ->sum('grand_total');

        $totalOrders = Order::count();

        $totalIncome = Payment::where('status', 'paid')->sum('grand_total');

        $days = (int) $request->input('days', 7);

        $omzetChart = $this->revenueChart($days);

        $latestOrder = Order::with('items.item')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        return response()->json([
            'message' => "Data dashboard berhasil diambil",
            'data' => [
                'todaysOrder' => $todaysOrder,
                'todaysIncome' => $todaysIncome,
                'totalOrders' => $totalOrders,
                'totalIncome' => $totalIncome,
                'omzetChart' => $omzetChart,
                'latestOrder' => OrderResource::collection($latestOrder)
            ]
        ]);
    }

    protected function revenueChart(int $days)
    {
        // // optional safety
        // $allowedDays = [7, 14, 30, 90];

        // if (! in_array($days, $allowedDays)) {
        //     $days = 7;
        // }

        $payments = Payment::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [
                now()->startOfDay()->subDays($days - 1),
                now()->endOfDay(),
            ])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $formatted = collect(range(0, $days - 1))
            ->map(function ($day) use ($payments, $days) {

                $date = now()
                    ->subDays(($days - 1) - $day)
                    ->format('Y-m-d');

                $payment = $payments->firstWhere('date', $date);

                return [
                    'date' => $date,
                    'label' => Carbon::parse($date)->translatedFormat('D'),
                    'total' => (int) ($payment->total ?? 0),
                ];
            });

        return $formatted;
    }
}
