<?php
namespace App\Application\UseCases\Store;

use App\Application\DTOs\Store\StoreProductDTO;
use App\Domain\Interfaces\StoreProductRepositoryInterface;
use App\Domain\Interfaces\StoreRepositoryInterface;
use Illuminate\Validation\ValidationException;

class AddProductToStoreUseCase
{
    public function __construct(
        private StoreProductRepositoryInterface $storeProductRepository,
        private StoreRepositoryInterface $storeRepository
    ) {}

    public function execute(StoreProductDTO $dto): StoreProductDTO
    {
        // Check if store exists
        $store = $this->storeRepository->findById($dto->store_id);
        if (!$store) {
            throw ValidationException::withMessages([
                'store_id' => 'Store not found.'
            ]);
        }

        // Check if product already exists in store
        $existing = $this->storeProductRepository->findByStoreAndMedicine(
            $dto->store_id,
            $dto->medicine_id
        );
        
        if ($existing) {
            throw ValidationException::withMessages([
                'medicine_id' => 'This product is already added to the store.'
            ]);
        }

        $storeProduct = $this->storeProductRepository->create($dto->toArray());
        
        return new StoreProductDTO(
            id: $storeProduct->id,
            store_id: $storeProduct->store_id,
            medicine_id: $storeProduct->medicine_id,
            buy_price: $storeProduct->buy_price,
            sale_price: $storeProduct->sale_price,
            wholesale_price: $storeProduct->wholesale_price,
            minimum_stock: $storeProduct->minimum_stock,
            is_active: $storeProduct->is_active,
            created_at: $storeProduct->created_at?->toISOString(),
            updated_at: $storeProduct->updated_at?->toISOString()
        );
    }
}