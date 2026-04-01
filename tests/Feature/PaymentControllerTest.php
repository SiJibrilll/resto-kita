<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TableSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_can_create_transaction(): void
    {
        $tableSession = TableSession::first();

        $responseCheckout = $this->withToken($tableSession->token)->postJson('/api/table-sessions/abc/checkout')->json();
        
        $response = $this->postJson('/api/payments/create', ['invoice_id' => $responseCheckout['data']['id'], 'customer_name' => 'John']);

        $payment = Payment::first();

        $response->assertJsonFragment([
            'snap_token' => $payment->snap_token
        ]);

        $response->assertStatus(200);
    }

    public function test_it_can_resend_an_existing_transaction(): void
    {
        $tableSession = TableSession::first();

        $responseCheckout = $this->withToken($tableSession->token)->postJson('/api/table-sessions/abc/checkout')->json();
        
        $responsePayment = $this->postJson('/api/payments/create', ['invoice_id' => $responseCheckout['data']['id'], 'customer_name' => 'John'])->json();

        $responseSecondPayment = $this->postJson('/api/payments/create', ['invoice_id' => $responseCheckout['data']['id'], 'customer_name' => 'John']);

        $responseSecondPayment->assertJsonFragment([
            'snap_token' => $responsePayment['data']['snap_token']
        ]);

        $responseSecondPayment->assertJsonFragment([
            'from_cache' => true
        ]);

        $responseSecondPayment->assertStatus(200);
    }

    public function test_it_can_check_for_payment_status(): void
    {
        $tableSession = TableSession::first();

        $responseCheckout = $this->withToken($tableSession->token)->postJson('/api/table-sessions/abc/checkout')->json();
        
        $responsePayment = $this->postJson('/api/payments/create', ['invoice_id' => $responseCheckout['data']['id'], 'customer_name' => 'John'])->json();

        $responseStatus = $this->getJson('/api/payments/status/' . $responseCheckout['data']['id']);

        $responseStatus->assertJsonStructure([
            'data' => [
                'status',
                'paid_at',
                'payment_method'
            ]
        ]);

        $responseStatus->assertStatus(200);
    }
}
