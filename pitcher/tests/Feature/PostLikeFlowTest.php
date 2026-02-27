<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostLikeFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_like_dislike_transition_updates_rating_without_drift()
    {
        $user = User::create([
            'login' => 'reactor_user',
            'full_name' => 'Reactor User',
            'email' => 'reactor_user@example.com',
            'password' => bcrypt('password123'),
            'role' => 'User',
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'First Post',
            'content' => 'Some content',
        ]);

        Sanctum::actingAs($user);

        $this->postJson("/api/posts/{$post->id}/like", ['type' => 'dislike'])
            ->assertStatus(201)
            ->assertJson(['message' => 'Post disliked']);

        $this->assertEquals(-1, $post->fresh()->rating);

        $this->postJson("/api/posts/{$post->id}/like", ['type' => 'like'])
            ->assertStatus(200)
            ->assertJson(['message' => 'Post liked']);

        $this->assertEquals(1, $post->fresh()->rating);

        $this->postJson("/api/posts/{$post->id}/like", ['type' => 'like'])
            ->assertStatus(200)
            ->assertJson(['message' => 'Post already liked']);

        $this->assertEquals(1, $post->fresh()->rating);
    }
}
