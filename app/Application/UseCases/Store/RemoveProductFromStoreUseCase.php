<?php
namespace App\Application\UseCases\Store;

use App\Domain\Interfaces\StoreProductRepositoryInterface;
use Illuminate\Validation\ValidationException;

class RemoveProductFromStoreUseCase
{
    public function __construct(
        private StoreProductRepositoryInterface $storeProductRepository
    ) {}

    public function execute(int $id): bool
    {
        // Check if store product exists
        $product = $this->storeProductRepository->findById($id);
        if (!$product) {
            throw ValidationException::withMessages([
                'product' => 'Product not found in store.'
            ]);
        }

        // Check if product has stock entries
        // You might want to prevent deletion if there are stock records
        // if ($product->stocks()->count() > 0) {
        //     throw ValidationException::withMessages([
        //         'product' => 'Cannot remove product with stock records. Remove stock first.'
        //     ]);
        // }

        return $this->storeProductRepository->delete($id);
    }
}