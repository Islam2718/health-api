<?php

namespace Tests\Feature\Api\BloodDonor;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BloodDonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_record_blood_donation(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/blood-donations', [
                'patient_name' => 'Karim Ahmed',
                'patient_gender' => 'Male',
                'patient_disease' => 'Thalassemia',
                'patient_blood_group' => 'O+',
                'donation_date' => now()->toDateString(),
                'hospital_name' => 'Dhaka Medical College Hospital',
                'hospital_address' => 'Dhaka',
                'units' => 1,
                'notes' => 'Emergency donation',
            ]);

        // dd(
        //     $response->status(),
        //     $response->getContent()
        // );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.donor_user_id',
                $user->id
            );

        $this->assertDatabaseHas('blood_donations', [
            'donor_user_id' => $user->id,
            'patient_name' => 'Karim Ahmed',
            'units' => 1,
        ]);
    }

    public function test_guest_cannot_record_donation(): void
    {
        $this
            ->postJson('/api/blood-donations', [])
            ->assertUnauthorized();
    }

    public function test_future_donation_date_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/blood-donations', [
                'patient_name' => 'Karim Ahmed',
                'donation_date' => now()
                    ->addDay()
                    ->toDateString(),
                'units' => 1,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'donation_date',
            ]);
    }

    public function test_donor_can_view_own_donation_history(): void
    {
        $user = User::factory()->create();

        $user->bloodDonations()->create([
            'patient_name' => 'Patient One',
            'donation_date' => '2026-08-01',
            'units' => 1,
        ]);

        $user->bloodDonations()->create([
            'patient_name' => 'Patient Two',
            'donation_date' => '2026-08-10',
            'units' => 1,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/my-blood-donations');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
