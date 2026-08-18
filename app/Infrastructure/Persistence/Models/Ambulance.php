<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ambulance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_model',
        'license_plate_number',
        'phone_number',
        'ambulance_type',
        'equipment_list',
        'description',
        'address',
        'is_active',
    ];

    protected $casts = [
        'equipment_list' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}