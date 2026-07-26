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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chamber()
    {
        return $this->belongsTo(Chamber::class);
    }
}
