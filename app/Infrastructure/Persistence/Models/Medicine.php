<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Database\Factories\MedicineFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicine extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\MedicineFactory::new();
    }

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

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            MedicineCategory::class,
            'medicine_category_id'
        );
    }
}
