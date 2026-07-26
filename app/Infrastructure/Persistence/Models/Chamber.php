<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class Chamber extends Model
{
    protected $table = 'chambers';

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'city',
        'area',
        'latitude',
        'longitude',
        'consultation_fee',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }
}
