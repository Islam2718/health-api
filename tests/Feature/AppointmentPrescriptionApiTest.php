<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Appointment;
use App\Infrastructure\Persistence\Models\AppointmentPrescription;
use App\Infrastructure\Persistence\Models\Chamber;
use App\Infrastructure\Persistence\Models\DoctorSchedule;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentPrescriptionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_fetch_a_prescription(): void
    {
        $doctor = User::factory()->create();
        $patient = User::factory()->create();

        $chamber = Chamber::create([
            'user_id' => $doctor->id,
            'name' => 'City Chamber',
            'address' => 'House 12, Road 3',
            'city' => 'Dhaka',
            'area' => 'Banani',
            'latitude' => '23.7800',
            'longitude' => '90.4100',
            'consultation_fee' => '1200.00',
            'is_active' => true,
        ]);

        $schedule = DoctorSchedule::create([
            'user_id' => $doctor->id,
            'chamber_id' => $chamber->id,
            'date' => now()->addDays(1)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'slot_duration' => 15,
            'max_patients' => 10,
            'consultation_fee' => '1200.00',
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'user_patient_id' => $patient->id,
            'user_doctor_id' => $doctor->id,
            'hospital_id' => null,
            'chamber_id' => $chamber->id,
            'doctor_schedule_id' => $schedule->id,
            'consultation_fee' => '1200.00',
            'discount' => '0.00',
            'appointment_type' => 'CHAMBER',
            'status' => 'APPROVED',
            'appointment_date' => now()->addDays(1)->toDateString(),
            'appointment_time' => '10:00:00',
        ]);

        $token = $doctor->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/appointment-prescriptions', [
                'appointment_id' => $appointment->id,
                'doctor_user_id' => $doctor->id,
                'patient_user_id' => $patient->id,
                'schedule_id' => $schedule->id,
                'chamber_id' => $chamber->id,
                'appointment_type' => 'CHAMBER',
                'blood_pressure_systolic' => 130,
                'blood_pressure_diastolic' => 85,
                'pulse' => 78,
                'is_smoking' => false,
                'sugar_level' => '5.8',
                'symptoms' => 'Headache and fatigue',
                'diagnosis' => 'Hypertension',
                'medicines' => [
                    [
                        'name' => 'Medicine A',
                        'dose' => '10mg',
                        'schedule' => '1+0+1',
                        'duration' => '7 days',
                        'notes' => 'After meals',
                    ],
                ],
                'prescription_date' => now()->toDateString(),
                'notes' => 'Review in one week',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.doctor_user_id', $doctor->id)
            ->assertJsonPath('data.patient_user_id', $patient->id)
            ->assertJsonPath('data.appointment_type', 'CHAMBER')
            ->assertJsonPath('data.medicines.0.name', 'Medicine A');

        $this->assertDatabaseHas('appointment_prescriptions', [
            'appointment_id' => $appointment->id,
            'doctor_user_id' => $doctor->id,
            'patient_user_id' => $patient->id,
            'blood_pressure_systolic' => 130,
        ]);
    }

    public function test_authenticated_doctor_can_list_prescriptions(): void
    {
        $doctor = User::factory()->create();
        $patient = User::factory()->create();

        $appointment = Appointment::create([
            'user_patient_id' => $patient->id,
            'user_doctor_id' => $doctor->id,
            'hospital_id' => null,
            'chamber_id' => null,
            'doctor_schedule_id' => null,
            'consultation_fee' => null,
            'discount' => null,
            'appointment_type' => 'ONLINE',
            'status' => 'APPROVED',
            'appointment_date' => now()->toDateString(),
            'appointment_time' => now()->toTimeString(),
        ]);

        $prescription = AppointmentPrescription::create([
            'appointment_id' => $appointment->id,
            'doctor_user_id' => $doctor->id,
            'patient_user_id' => $patient->id,
            'appointment_type' => 'ONLINE',
            'medicines' => [
                ['name' => 'Medicine B', 'schedule' => '1+0+0'],
            ],
            'prescription_date' => now()->toDateString(),
        ]);

        $token = $doctor->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/appointment-prescriptions');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $prescription->id);
    }

    public function test_authenticated_patient_can_list_my_prescriptions(): void
    {
        $doctor = User::factory()->create();
        $patient = User::factory()->create();

        $appointment = Appointment::create([
            'user_patient_id' => $patient->id,
            'user_doctor_id' => $doctor->id,
            'hospital_id' => null,
            'chamber_id' => null,
            'doctor_schedule_id' => null,
            'consultation_fee' => null,
            'discount' => null,
            'appointment_type' => 'ONLINE',
            'status' => 'APPROVED',
            'appointment_date' => now()->toDateString(),
            'appointment_time' => now()->toTimeString(),
        ]);

        $prescription = AppointmentPrescription::create([
            'appointment_id' => $appointment->id,
            'doctor_user_id' => $doctor->id,
            'patient_user_id' => $patient->id,
            'appointment_type' => 'ONLINE',
            'medicines' => [
                ['name' => 'Medicine C', 'schedule' => '1+0+0'],
            ],
            'prescription_date' => now()->toDateString(),
        ]);

        $token = $patient->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/my-prescriptions');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $prescription->id);
    }
}
