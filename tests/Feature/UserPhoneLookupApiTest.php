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
        $user = User::factory()->create(['phone' => '01710001337']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users/phone/01710001337');

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.phone', '01710001337')
            ->assertJsonPath('data.appointments', [])
            ->assertJsonPath('data.prescriptions', []);
    }

    public function test_creates_user_when_phone_not_found(): void
    {
        $doctor = User::factory()->create();
        $token = $doctor->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users/phone/01710001337', [
                'name' => 'New Patient',
                'password' => 'password',
                'email' => 'ibno@health.com',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.phone', '01710001337')
            ->assertJsonPath('data.name', 'New Patient')
            ->assertJsonPath('data.prescriptions', [])
            ->assertJsonPath('data.appointments', []);

        $this->assertDatabaseHas('users', ['phone' => '01710001337', 'email' => 'ibno@health.com']);
    }
}
