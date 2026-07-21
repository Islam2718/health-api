<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_profile_details(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'password' => 'secret123',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/profile', [
                'type' => 'DOCTOR',
                'gender' => 'female',
                'date_of_birth' => '1990-01-02',
                'profile_image' => 'avatars/test.png',
                'address' => 'Dhaka, Bangladesh',
                'blood_group' => 'A+',
                'marital_status' => 'Married',
            ]);

        $response->assertOk()
            ->assertJsonPath('user.type', 'DOCTOR')
            ->assertJsonPath('user.gender', 'female')
            ->assertJsonPath('user.address', 'Dhaka, Bangladesh')
            ->assertJsonPath('user.blood_group', 'A+')
            ->assertJsonPath('user.marital_status', 'Married');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'type' => 'DOCTOR',
            'gender' => 'female',
            'address' => 'Dhaka, Bangladesh',
            'blood_group' => 'A+',
            'marital_status' => 'Married',
        ]);
    }
}
