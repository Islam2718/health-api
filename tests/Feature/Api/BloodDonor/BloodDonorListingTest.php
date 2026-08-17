<?php

namespace Tests\Feature\Api\BloodDonor;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BloodDonorListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_interested_users_are_returned(): void
    {
        $donor = User::factory()->create([
            'donor_interest' => true,
            'blood_group' => 'O+',
        ]);

        User::factory()->create([
            'donor_interest' => false,
            'blood_group' => 'O+',
        ]);

        $response = $this
            ->actingAs($donor, 'sanctum')
            ->getJson('/api/blood-donors');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $donor->id
            );
    }

    public function test_donor_can_be_filtered_by_blood_group(): void
    {
        $donor = User::factory()->create([
            'donor_interest' => true,
            'blood_group' => 'O+',
        ]);

        User::factory()->create([
            'donor_interest' => true,
            'blood_group' => 'A+',
        ]);

        $response = $this
            ->actingAs($donor, 'sanctum')
            ->getJson('/api/blood-donors?blood_group=O%2B');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.blood_group',
                'O+'
            );
    }
}