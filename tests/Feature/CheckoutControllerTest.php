<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\TableSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_can_checkout_and_generate_invoice(): void
    {
        $tableSession = TableSession::first();

        $response = $this->withToken($tableSession->token)->post('/api/table-sessions/abc/checkout');

        $response->assertStatus(201);
        $response->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas(TableSession::class, [
            'id' => $tableSession->id,
            'status' => 'checked_out'
        ]);

        $this->assertDatabaseHas(Invoice::class, [
            'table_session_id' => $tableSession->id
        ]);
    }
}
