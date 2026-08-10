<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Appointment;
use App\Infrastructure\Persistence\Models\Chamber;
use App\Infrastructure\Persistence\Models\DoctorSchedule;
use App\Infrastructure\Persistence\Models\Hospital;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_fetch_an_appointment(): void
    {
        $patient = User::factory()->create();
        $doctor = User::factory()->create();

        $hospital = Hospital::create([
            'user_id' => $doctor->id,
            'name' => 'City Hospital',
            'phone' => '0123456789',
            'email' => 'hospital@example.com',
            'address' => 'Health Street',
            'city' => 'Dhaka',
            'is_active' => true,
        ]);

        $chamber = Chamber::create([
            'user_id' => $doctor->id,
            'name' => 'City Chamber',
            'address' => 'House 12, Road 3',
            'city' => 'Dhaka',
            'area' => 'Banani',
            'latitude' => '23.7800',
            'longitude' => '90.4100',
            'consultation_fee' => '1000.00',
            'is_active' => true,
        ]);

        $schedule = DoctorSchedule::create([
            'user_id' => $doctor->id,
            'chamber_id' => $chamber->id,
            'date' => now()->addDays(1)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
            'slot_duration' => 15,
            'max_patients' => 20,
            'consultation_fee' => '1500.00',
            'is_active' => true,
        ]);

        $token = $patient->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/appointments', [
                'user_doctor_id' => $doctor->id,
                'hospital_id' => $hospital->id,
                'chamber_id' => $chamber->id,
                'doctor_schedule_id' => $schedule->id,
                'consultation_fee' => '1000.00',
                'discount' => '100.00',
                'appointment_type' => 'CHAMBER',
                'status' => 'PENDING',
                'appointment_date' => now()->addDays(2)->toDateString(),
                'appointment_time' => '10:00:00',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.user_patient_id', $patient->id)
            ->assertJsonPath('data.user_doctor_id', $doctor->id)
            ->assertJsonPath('data.chamber_id', $chamber->id)
            ->assertJsonPath('data.appointment_type', 'CHAMBER');

        $appointment = Appointment::first();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'user_patient_id' => $patient->id,
            'user_doctor_id' => $doctor->id,
            'appointment_type' => 'CHAMBER',
            'consultation_fee' => '1000.00',
        ]);
    }

    public function test_authenticated_user_can_create_appointment_with_consultation_fee(): void
    {
        $patient = User::factory()->create();
        $doctor = User::factory()->create();

        $token = $patient->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/appointments', [
                'user_doctor_id' => $doctor->id,
                'appointment_date' => now()->addDays(2)->toDateString(),
                'consultation_fee' => '1200.00',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.consultation_fee', '1200.00');

        $appointment = Appointment::first();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'consultation_fee' => '1200.00',
        ]);
    }

    public function test_authenticated_doctor_can_list_upcoming_appointments(): void
    {
        $patient = User::factory()->create();
        $doctor = User::factory()->create();
        $token = $doctor->createToken('test-token')->plainTextToken;

        Appointment::create([
            'user_patient_id' => $patient->id,
            'user_doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(3)->toDateString(),
            'appointment_time' => '11:00:00',
            'appointment_type' => 'ONLINE',
            'status' => 'PENDING',
        ]);

        Appointment::create([
            'user_patient_id' => $patient->id,
            'user_doctor_id' => $doctor->id,
            'appointment_date' => now()->subDays(1)->toDateString(),
            'appointment_time' => '09:00:00',
            'appointment_type' => 'ONLINE',
            'status' => 'APPROVED',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/appointments/upcoming');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.status', 'PENDING');
    }

    public function test_authenticated_patient_can_list_my_appointments(): void
    {
        $patient = User::factory()->create();
        $doctor = User::factory()->create();
        $token = $patient->createToken('test-token')->plainTextToken;

        Appointment::create([
            'user_patient_id' => $patient->id,
            'user_doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(1)->toDateString(),
            'appointment_time' => '09:00:00',
            'appointment_type' => 'ONLINE',
            'status' => 'APPROVED',
        ]);

        Appointment::create([
            'user_patient_id' => $patient->id,
            'user_doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '10:00:00',
            'appointment_type' => 'ONLINE',
            'status' => 'PENDING',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/my-appointments');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.user_patient_id', $patient->id);
    }
}
