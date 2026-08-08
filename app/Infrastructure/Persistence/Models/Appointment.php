<?php

namespace App\Infrastructure\Persistence\Models;

use App\Infrastructure\Persistence\Models\Chamber;
use App\Infrastructure\Persistence\Models\Hospital;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'user_patient_id',
        'user_doctor_id',
        'hospital_id',
        'chamber_id',
        'doctor_schedule_id',
        'consultation_fee',
        'discount',
        'appointment_type',
        'status',
        'appointment_date',
        'appointment_time',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'consultation_fee' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    protected $appends = [
        'hospital_name',
        'chamber_name',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'user_patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'user_doctor_id');
    }

    public function user_patient()
    {
        return $this->patient();
    }

    public function user_doctor()
    {
        return $this->doctor();
    }

    public function userPatient()
    {
        return $this->patient();
    }

    public function userDoctor()
    {
        return $this->doctor();
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function chamber()
    {
        return $this->belongsTo(Chamber::class);
    }

    public function doctorSchedule()
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id');
    }

    public function getHospitalNameAttribute(): ?string
    {
        return $this->hospital?->name;
    }

    public function getChamberNameAttribute(): ?string
    {
        return $this->chamber?->name;
    }
}
