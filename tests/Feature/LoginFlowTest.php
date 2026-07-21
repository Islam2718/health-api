<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_registered_credentials(): void
    {
        $user = User::create([
            'name' => 'Login User',
            'email' => 'login@example.com',
            'phone' => '9999999999',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/login', [
            'identifier' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('token'));
    }
}
