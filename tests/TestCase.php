<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected $token = 'abc';
    
    use RefreshDatabase;

    protected $seed = true;

    protected $user;
    

    protected function actingAsSanctum(?User $user = null, array $abilities = ['*'])
    {
        $this->user = $user ?? User::factory()->create();

        Sanctum::actingAs($this->user, $abilities);

        return $this;
    }
}
