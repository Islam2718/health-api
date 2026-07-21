<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalExperience extends Model
{
    protected $table = 'professional_experiences';

    protected $fillable = [
        'user_id',
        'job_title',
        'company_name',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];
}
