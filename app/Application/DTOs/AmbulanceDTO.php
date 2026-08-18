<?php

namespace App\Application\DTOs;

class AmbulanceDTO
{
    public function __construct(
        public readonly ?int $userId,
        public readonly ?string $brandModel = null,
        public readonly ?string $licensePlateNumber = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $ambulanceType = null,
        public readonly ?array $equipmentList = null,
        public readonly ?string $description = null,
        public readonly ?string $address = null,
        public readonly bool $isActive = true,
    ) {
    }

    public static function fromArray(
        array $data,
        ?int $userId = null
    ): self {
        return new self(
            userId: $userId ?? ($data['user_id'] ?? null),
            brandModel: $data['brand_model'] ?? null,
            licensePlateNumber: $data['license_plate_number'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            ambulanceType: $data['ambulance_type'] ?? null,
            equipmentList: $data['equipment_list'] ?? null,
            description: $data['description'] ?? null,
            address: $data['address'] ?? null,
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'brand_model' => $this->brandModel,
            'license_plate_number' => $this->licensePlateNumber,
            'phone_number' => $this->phoneNumber,
            'ambulance_type' => $this->ambulanceType,
            'equipment_list' => $this->equipmentList,
            'description' => $this->description,
            'address' => $this->address,
            'is_active' => $this->isActive,
        ];
    }
}