<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_protects_admin_endpoint(): void
    {
        $response = $this->getJson(route('admin.items.index'));

        $response->assertStatus(401);
    }

    function test_it_can_login() {
        $user = User::where('email', 'test@example.com')->first();

        $payload = [
            "email" => $user->email,
            'password' => 'password'
        ];

        $response = $this->postJson(route('login'), $payload);

        $response->assertStatus(200);
    }
}
