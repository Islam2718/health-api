<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Models\User;

class Doctor extends Model
{
    protected $table = 'doctors';

    protected $fillable = [
        'user_id',
        'title',
        'specialization',
        'license_number',
        'bio',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chambers()
    {
        return $this->hasMany(Chamber::class, 'user_id', 'user_id');
    }

    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'user_id', 'user_id');
    }
}
