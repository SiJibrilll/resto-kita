<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\TableSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use function Symfony\Component\Clock\now;

class TableSessionController extends Controller
{
    function generateSession(Request $request) {
        $validated = $request->validate([
            'table_id' => ['required', 'numeric', 'exists:tables,id']
        ]);

        $table = Table::find($validated['table_id']);

        if ($table->hasActiveSession()) {
            return response()->json([
                'message' => 'Maaf, meja sedang digunakan'
            ], 405);
        }

        $session = TableSession::create([
            'table_id' => $table->id,
            'token' => Str::ulid(),
            'seated_at' => now()
        ]);

         return response()->json( [
            'data' => [
                'token' => $session->token,
                'table_id' => $session->table_id,
                'seated_at' => $session->seated_at
            ]
        ]);
    }
}
