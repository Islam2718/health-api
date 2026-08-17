<?php

namespace Tests\Feature\Api\BloodDonor;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonorInterestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_enable_donor_interest(): void
    {
        $user = User::factory()->create([
            'donor_interest' => false,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->patchJson('/api/blood-donors/interest', [
                'donor_interest' => true,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.donor_interest',
                true
            );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'donor_interest' => true,
        ]);
    }

    public function test_authenticated_user_can_disable_donor_interest(): void
    {
        $user = User::factory()->create([
            'donor_interest' => true,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->patchJson('/api/blood-donors/interest', [
                'donor_interest' => false,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.donor_interest',
                false
            );
    }

    public function test_guest_cannot_update_donor_interest(): void
    {
        $this
            ->patchJson('/api/blood-donors/interest', [
                'donor_interest' => true,
            ])
            ->assertUnauthorized();
    }
}