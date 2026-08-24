<?php
namespace App\Application\UseCases\Store;

use App\Application\DTOs\Store\StoreProductDTO;
use App\Domain\Interfaces\StoreProductRepositoryInterface;
use Illuminate\Validation\ValidationException;

class UpdateStoreProductUseCase
{
    public function __construct(
        private StoreProductRepositoryInterface $storeProductRepository
    ) {}

    public function execute(StoreProductDTO $dto): StoreProductDTO
    {
        // Check if store product exists
        $existingProduct = $this->storeProductRepository->findById($dto->id);
        if (!$existingProduct) {
            throw ValidationException::withMessages([
                'product' => 'Product not found in store.'
            ]);
        }

        // Check if trying to change medicine and if it already exists in store
        if ($dto->medicine_id !== $existingProduct->medicine_id) {
            $duplicate = $this->storeProductRepository->findByStoreAndMedicine(
                $dto->store_id,
                $dto->medicine_id
            );
            if ($duplicate && $duplicate->id !== $dto->id) {
                throw ValidationException::withMessages([
                    'medicine_id' => 'This product is already added to the store.'
                ]);
            }
        }

        $storeProduct = $this->storeProductRepository->update($dto->id, $dto->toArray());
        
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