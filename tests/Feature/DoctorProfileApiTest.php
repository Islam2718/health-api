<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Chamber;
use App\Infrastructure\Persistence\Models\Doctor;
use App\Infrastructure\Persistence\Models\DoctorSchedule;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_fetch_doctor_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/doctors', [
                'title' => 'Dr.',
                'specialization' => 'Cardiology',
                'license_number' => 'LIC-1001',
                'bio' => 'Heart specialist',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Dr.')
            ->assertJsonPath('data.specialization', 'Cardiology');

        $this->assertDatabaseHas('doctors', [
            'user_id' => $user->id,
            'title' => 'Dr.',
            'specialization' => 'Cardiology',
        ]);
    }

    public function test_can_fetch_public_doctor_listing_without_authentication(): void
    {
        $doctorUser = User::factory()->create(['name' => 'Dr. Shahriar', 'address' => 'Banani, Dhaka']);
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'title' => 'Dr.',
            'specialization' => 'Cardiology',
            'license_number' => 'LIC-1002',
            'bio' => 'Heart specialist',
            'is_active' => true,
        ]);

        $otherUser = User::factory()->create(['name' => 'Dr. Rina', 'address' => 'Gulshan, Dhaka']);
        Doctor::create([
            'user_id' => $otherUser->id,
            'title' => 'Dr.',
            'specialization' => 'Neurology',
            'license_number' => 'LIC-1003',
            'bio' => 'Brain specialist',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/doctors/public?designation=Dr.&department=Cardiology&address=Banani');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.specialization', 'Cardiology');
        $response->assertJsonPath('data.0.user.name', 'Dr. Shahriar');
    }

    public function test_authenticated_user_can_create_and_fetch_education_record(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/educations', [
                'institution' => 'Dhaka Medical College',
                'degree' => 'MBBS',
                'field_of_study' => 'Medicine',
                'start_date' => '2010-01-01',
                'end_date' => '2015-12-31',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.institution', 'Dhaka Medical College')
            ->assertJsonPath('data.degree', 'MBBS');

        $this->assertDatabaseHas('educations', [
            'user_id' => $user->id,
            'institution' => 'Dhaka Medical College',
            'degree' => 'MBBS',
        ]);
    }

    public function test_authenticated_user_can_create_and_fetch_professional_experience(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/professional-experiences', [
                'job_title' => 'Senior Surgeon',
                'company_name' => 'City Hospital',
                'location' => 'Dhaka',
                'start_date' => '2018-01-01',
                'end_date' => '2024-12-31',
                'is_current' => false,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.job_title', 'Senior Surgeon')
            ->assertJsonPath('data.company_name', 'City Hospital');

        $this->assertDatabaseHas('professional_experiences', [
            'user_id' => $user->id,
            'job_title' => 'Senior Surgeon',
            'company_name' => 'City Hospital',
        ]);
    }
}
