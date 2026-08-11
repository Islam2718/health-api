<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPhoneLookupApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_existing_user_by_phone(): void
    {
        $user = User::factory()->create(['phone' => '01712345678']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users/phone/01712345678');

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.phone', '01712345678')
            ->assertJsonPath('appointments', []);
    }

    public function test_creates_user_when_phone_not_found(): void
    {
        $doctor = User::factory()->create();
        $token = $doctor->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users/phone/01787654321', [
                'name' => 'New Patient',
                'password' => 'password',
                'email' => 'patient@example.com',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.phone', '01787654321')
            ->assertJsonPath('data.name', 'New Patient')
            ->assertJsonPath('appointments', []);

        $this->assertDatabaseHas('users', ['phone' => '01787654321', 'email' => 'patient@example.com']);
    }
}
