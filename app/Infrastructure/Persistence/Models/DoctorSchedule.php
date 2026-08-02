<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    protected $table = 'doctor_schedules';

    protected $fillable = [
        'user_id',
        'chamber_id',
        'date',
        'start_time',
        'end_time',
        'slot_duration',
        'max_patients',
        'consultation_fee',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'consultation_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chamber()
    {
        return $this->belongsTo(Chamber::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_schedule_id');
    }
}

