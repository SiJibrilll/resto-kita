<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminCategoryControllerTest extends TestCase
{
    use RefreshDatabase; // Automatically resets the DB
    /**
     * A basic feature test example.
     */
    public function test_it_can_show_category_for_admin(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }

    function test_it_can_store_category_for_admin() {
        $payload = [
            'name' => 'Drinks',
            'description' => "Minuman segar"
        ];

        $response = $this->postJson(route('admin.categories.store'), $payload);

        $response->assertJsonFragment([
            'name' => $payload['name']
        ]);

        $this->assertDatabaseHas('categories', ['name' => $payload['name']]);
    }

    function test_it_can_update_category_for_admin() {
        $id = 1;
        $payload = [
            'name' => 'Food',
            'description' => "Makanan"
        ];

        $response = $this->putJson(route('admin.categories.update', ['category' => $id]), $payload);

        $response->assertJsonFragment([
            'name' => $payload['name']
        ]);

        $this->assertDatabaseHas('categories', ['name' => $payload['name'], 'id' => $id]);
    }

    function test_it_can_delete_category_for_admin() {
        $category = Category::find(2);

        $response = $this->deleteJson(route('admin.categories.destroy', ['category' => $category->id]));

        $response->assertStatus(200);

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }
}
