<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Database\Factories\MedicineFactory;

class Medicine extends Model
{
    use HasFactory;

    protected $table = 'medicines';

    protected $fillable = [
        'name',
        'generic_name',
        'weight',
        'suggestion_price',
        'type',
        'description',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(MedicineCompany::class, 'company_id');
    }
}
