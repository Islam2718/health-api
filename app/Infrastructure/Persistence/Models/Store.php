<?php
namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Database\Factories\StoreFactory;

class Store extends Model
{
    use HasFactory;
    
    protected static function newFactory()
    {
        return \Database\Factories\StoreFactory::new();
    }

    protected $table = 'stores';

    protected $fillable = [
        'user_id',
        'store_name',
        'store_address',
        'trade_license_no',
        'phone',
        'email',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storeProducts()
    {
        return $this->hasMany(StoreProduct::class);
    }
}