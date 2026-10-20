<?php
namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Database\Factories\StoreProductFactory;

class StoreProduct extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\StoreProductFactory::new();
    }    

    protected $table = 'store_products';

    protected $fillable = [
        'store_id',
        'medicine_id',
        'buy_price',
        'sale_price',
        'wholesale_price',
        'minimum_stock',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'buy_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getCurrentStockAttribute()
    {
        return $this->stocks()->where('transaction_type', 'purchase')->sum('quantity')
            - $this->orderItems()->sum('quantity');
    }
}