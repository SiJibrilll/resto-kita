<?php

namespace Tests\Feature;

use App\Http\Resources\OrderResource;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    function test_it_can_list_orders_of_a_table() {
        $orders = Order::with('items.item')->first();
        $item = $orders->items->first();

        $response = $this->withToken($this->token)->getJson('api/orders');

        $response->assertOk();

        $response->assertJsonPath('data.0.id', $orders->id);

        $response->assertJsonPath('data.0.order_items.0.id', $item->id);

    }

    function test_it_can_place_an_order() {
        $item1 = Item::first();
        $item2 = Item::skip(1)->first();

        $payload = [
            "orders" => [
                ['item_id' => $item1->id, "amount" => 2],
                ['item_id' => $item2->id, "amount" => 1]
            ]
        ];

        $response = $this->withToken($this->token)->postJson('api/orders', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.confirmed', true);
        $response->assertJsonPath('data.order_items.0.item.id', $item1->id);
    }
}
