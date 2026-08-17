<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodDonation extends Model
{
    protected $table = 'blood_donations';

    protected $fillable = [
        'donor_user_id',
        'patient_name',
        'patient_gender',
        'patient_disease',
        'patient_blood_group',
        'donation_date',
        'hospital_name',
        'hospital_address',
        'units',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'donation_date' => 'date',
            'units' => 'integer',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'donor_user_id'
        );
    }
}