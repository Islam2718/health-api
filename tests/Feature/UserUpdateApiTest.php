<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUpdateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_update_api_can_change_profile_fields(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '1111111111',
            'password' => 'secret123',
            'type' => 'ADMIN',
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/users/' . $admin->id, [
                'type' => 'DOCTOR',
                'gender' => 'female',
                'date_of_birth' => '1990-01-02',
                'address' => 'Dhaka, Bangladesh',
                'blood_group' => 'A+',
                'marital_status' => 'Married',
            ]);

        $response->assertOk()
            ->assertJsonPath('type', 'DOCTOR')
            ->assertJsonPath('gender', 'female')
            ->assertJsonPath('address', 'Dhaka, Bangladesh')
            ->assertJsonPath('blood_group', 'A+')
            ->assertJsonPath('marital_status', 'Married');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'type' => 'DOCTOR',
            'gender' => 'female',
            'address' => 'Dhaka, Bangladesh',
            'blood_group' => 'A+',
            'marital_status' => 'Married',
        ]);
    }
}
