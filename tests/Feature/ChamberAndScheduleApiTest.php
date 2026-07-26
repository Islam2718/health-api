<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChamberAndScheduleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_fetch_chamber(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/chambers', [
                'name' => 'City Chamber',
                'address' => 'House 12, Road 3',
                'city' => 'Dhaka',
                'area' => 'Banani',
                'latitude' => '23.7800',
                'longitude' => '90.4100',
                'consultation_fee' => '1000.00',
                'is_active' => true,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'City Chamber')
            ->assertJsonPath('data.user_id', $user->id);

        $this->assertDatabaseHas('chambers', [
            'user_id' => $user->id,
            'name' => 'City Chamber',
            'city' => 'Dhaka',
            'area' => 'Banani',
        ]);
    }

    public function test_authenticated_user_can_create_and_fetch_doctor_schedule(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/chambers', [
                'name' => 'City Chamber',
                'address' => 'House 12, Road 3',
                'city' => 'Dhaka',
                'area' => 'Banani',
                'latitude' => '23.7800',
                'longitude' => '90.4100',
                'consultation_fee' => '1000.00',
                'is_active' => true,
            ]);

        $chamber = $user->chambers()->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/doctor-schedules', [
                'chamber_id' => $chamber->id,
                'date' => '2026-07-27',
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'slot_duration' => 15,
                'max_patients' => 20,
                'is_active' => true,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.chamber_id', $chamber->id)
            ->assertJsonPath('data.user_id', $user->id);

        $this->assertDatabaseHas('doctor_schedules', [
            'user_id' => $user->id,
            'chamber_id' => $chamber->id,
            'date' => '2026-07-27',
            'slot_duration' => 15,
            'max_patients' => 20,
        ]);
    }
}
