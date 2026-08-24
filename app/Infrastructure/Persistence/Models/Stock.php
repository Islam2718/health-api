<?php
namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Database\Factories\StockFactory;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'store_product_id',
        'quantity',
        'transaction_type',
        'unit_price',
        'total_price',
        'remarks',
        'transaction_date'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'transaction_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',        
    ];

    public function storeProduct()
    {
        return $this->belongsTo(StoreProduct::class);
    }
}