<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_rotates_old_tokens()
    {
        $user = User::create([
            'login' => 'pitcher_user',
            'full_name' => 'Pitcher User',
            'email' => 'pitcher_user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'User',
        ]);

        $user->createToken('old-token');
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $response = $this->postJson('/api/auth/login', [
            'login' => 'pitcher_user',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'login', 'full_name', 'email'],
                'token',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }
}
