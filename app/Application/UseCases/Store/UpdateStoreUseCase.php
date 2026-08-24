<?php
namespace App\Application\UseCases\Store;

use App\Application\DTOs\Store\StoreDTO;
use App\Domain\Interfaces\StoreRepositoryInterface;
use Illuminate\Validation\ValidationException;

class UpdateStoreUseCase
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository
    ) {}

    public function execute(StoreDTO $dto): StoreDTO
    {
        // Check if store exists
        $existingStore = $this->storeRepository->findById($dto->id);
        if (!$existingStore) {
            throw ValidationException::withMessages([
                'store' => 'Store not found.'
            ]);
        }

        // Check if trade license exists for other stores
        $storeWithLicense = $this->storeRepository->getByLicenseNo($dto->trade_license_no);
        if ($storeWithLicense && $storeWithLicense->id !== $dto->id) {
            throw ValidationException::withMessages([
                'trade_license_no' => 'This trade license number is already registered.'
            ]);
        }

        $store = $this->storeRepository->update($dto->id, $dto->toArray());
        
        return new StoreDTO(
            id: $store->id,
            user_id: $store->user_id,
            store_name: $store->store_name,
            store_address: $store->store_address,
            trade_license_no: $store->trade_license_no,
            phone: $store->phone,
            email: $store->email,
            description: $store->description,
            is_active: $store->is_active,
            created_at: $store->created_at?->toISOString(),
            updated_at: $store->updated_at?->toISOString()
        );
    }
}