<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_created_without_description()
    {
        $user = User::create([
            'login' => 'category_user',
            'full_name' => 'Category User',
            'email' => 'category_user@example.com',
            'password' => bcrypt('password123'),
            'role' => 'User',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/categories', [
            'title' => 'Backend',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('categories', [
            'title' => 'Backend',
            'description' => null,
        ]);
    }
}
