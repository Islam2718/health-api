<?php
namespace App\Domain\Entities;

class Stock
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $store_product_id,
        public readonly int $quantity,
        public readonly string $transaction_type,
        public readonly float $unit_price,
        public readonly float $total_price,
        public readonly ?string $remarks,
        public readonly string $transaction_date,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'store_product_id' => $this->store_product_id,
            'quantity' => $this->quantity,
            'transaction_type' => $this->transaction_type,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'remarks' => $this->remarks,
            'transaction_date' => $this->transaction_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ], fn($value) => !is_null($value));
    }
}