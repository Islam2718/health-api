<?php
namespace App\Application\DTOs\Store;

class StoreDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $user_id,
        public readonly string $store_name,
        public readonly string $store_address,
        public readonly string $trade_license_no,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly ?string $description,
        public readonly bool $is_active = true,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'store_name' => $this->store_name,
            'store_address' => $this->store_address,
            'trade_license_no' => $this->trade_license_no,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ], fn($value) => !is_null($value));
    }
}