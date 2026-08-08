<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Models\Medicine;

class MedicineCompany extends Model
{
    use HasFactory;

    protected $table = 'medicine_companies';

    protected $fillable = [
        'name',
        'logo',
        'address',
        'license_number',
        'about',
    ];

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'company_id');
    }
}
