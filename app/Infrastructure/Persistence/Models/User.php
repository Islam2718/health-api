<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Persistence\Models\BloodDonation;

class User extends Authenticatable
{
    // use HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'type',
        'gender',
        'date_of_birth',
        'profile_image',
        'address',
        'blood_group',
        'marital_status',
        'is_active',
        'donor_interest'
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * type Casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'donor_interest' => 'boolean',
    ];

    /**
     * Auto hash password
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function chambers()
    {
        return $this->hasMany(Chamber::class);
    }

    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointmentsAsPatient()
    {
        return $this->hasMany(Appointment::class, 'user_patient_id');
    }

    public function appointmentsAsDoctor()
    {
        return $this->hasMany(Appointment::class, 'user_doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(AppointmentPrescription::class, 'patient_user_id');
    }
    public function bloodDonations(): HasMany
    {
        return $this->hasMany(
            BloodDonation::class,
            'donor_user_id'
        );
    }
}
