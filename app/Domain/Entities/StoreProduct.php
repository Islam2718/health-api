<?php
namespace App\Domain\Entities;

class StoreProduct
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $store_id,
        public readonly int $medicine_id,
        public readonly float $buy_price,
        public readonly float $sale_price,
        public readonly float $wholesale_price,
        public readonly int $minimum_stock = 5,
        public readonly bool $is_active = true,
        public readonly ?int $current_stock = null,
        public readonly ?string $medicine_name = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'store_id' => $this->store_id,
            'medicine_id' => $this->medicine_id,
            'buy_price' => $this->buy_price,
            'sale_price' => $this->sale_price,
            'wholesale_price' => $this->wholesale_price,
            'minimum_stock' => $this->minimum_stock,
            'is_active' => $this->is_active,
            'current_stock' => $this->current_stock,
            'medicine_name' => $this->medicine_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ], fn($value) => !is_null($value));
    }
}