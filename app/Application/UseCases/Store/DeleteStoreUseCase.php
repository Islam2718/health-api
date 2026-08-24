<?php
namespace App\Application\UseCases\Store;

use App\Domain\Interfaces\StoreRepositoryInterface;
use Illuminate\Validation\ValidationException;

class DeleteStoreUseCase
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository
    ) {}

    public function execute(int $id): bool
    {
        // Check if store exists
        $store = $this->storeRepository->findById($id);
        if (!$store) {
            throw ValidationException::withMessages([
                'store' => 'Store not found.'
            ]);
        }

        // You might want to check if store has products before deleting
        // if ($store->storeProducts()->count() > 0) {
        //     throw ValidationException::withMessages([
        //         'store' => 'Cannot delete store with products. Remove products first.'
        //     ]);
        // }

        return $this->storeRepository->delete($id);
    }
}