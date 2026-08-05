<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentPrescription extends Model
{
    protected $table = 'appointment_prescriptions';

    protected $fillable = [
        'appointment_id',
        'doctor_user_id',
        'patient_user_id',
        'schedule_id',
        'chamber_id',
        'appointment_type',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'pulse',
        'is_smoking',
        'sugar_level',
        'symptoms',
        'diagnosis',
        'medicines',
        'prescription_date',
        'notes',
    ];

    protected $casts = [
        'is_smoking' => 'boolean',
        'medicines' => 'array',
        'prescription_date' => 'date',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_user_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    public function schedule()
    {
        return $this->belongsTo(DoctorSchedule::class, 'schedule_id');
    }

    public function chamber()
    {
        return $this->belongsTo(Chamber::class);
    }
}
