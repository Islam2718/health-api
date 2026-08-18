<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Ambulance;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AmbulanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_ambulance(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/ambulances', [
                'ambulance_type' => 'AC',
                'brand_model' => 'Toyota Hiace',
                'license_plate_number' => 'DHAKA-TA-1234',
                'phone_number' => '01700000000',
                'equipment_list' => [
                    'Ventilator',
                    'Oxygen',
                    'ICU',
                ],
                'description' => 'Fully equipped ICU ambulance.',
                'address' => 'Dhaka, Bangladesh',
                'is_active' => true,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.brand_model',
                'Toyota Hiace'
            );

        $this->assertDatabaseHas(
            'ambulances',
            [
                'user_id' => $user->id,
                'brand_model' => 'Toyota Hiace',
                'license_plate_number' => 'DHAKA-TA-1234',
                'ambulance_type' => 'AC',
            ]
        );
    }

    public function test_user_can_only_update_own_ambulance(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $ambulance = Ambulance::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->putJson(
                "/api/ambulances/{$ambulance->id}",
                [
                    'brand_model' => 'Changed Model',
                ]
            );

        $response->assertNotFound();
    }

    public function test_public_user_can_view_active_ambulances(): void
    {
        $user = User::factory()->create();

        Ambulance::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $response = $this->getJson(
            '/api/ambulances/public'
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_inactive_ambulance_is_not_public(): void
    {
        $user = User::factory()->create();

        $ambulance = Ambulance::factory()->create([
            'user_id' => $user->id,
            'is_active' => false,
        ]);

        $response = $this->getJson(
            "/api/ambulances/public/{$ambulance->id}"
        );

        $response->assertNotFound();
    }

    public function test_public_response_contains_expected_ambulance_fields(): void
    {
        $user = User::factory()->create();

        $ambulance = Ambulance::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
            'brand_model' => 'Toyota Hiace',
            'license_plate_number' => 'DHAKA-TA-1234',
            'phone_number' => '01700000000',
            'ambulance_type' => 'AC',
            'equipment_list' => [
                'Ventilator',
                'Oxygen',
            ],
            'description' => 'ICU ambulance',
            'address' => 'Dhaka, Bangladesh',
        ]);

        $response = $this->getJson(
            "/api/ambulances/public/{$ambulance->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.brand_model',
                'Toyota Hiace'
            )
            ->assertJsonPath(
                'data.license_plate_number',
                'DHAKA-TA-1234'
            )
            ->assertJsonPath(
                'data.phone_number',
                '01700000000'
            )
            ->assertJsonPath(
                'data.ambulance_type',
                'AC'
            )
            ->assertJsonPath(
                'data.description',
                'ICU ambulance'
            )
            ->assertJsonPath(
                'data.address',
                'Dhaka, Bangladesh'
            );
    }
}