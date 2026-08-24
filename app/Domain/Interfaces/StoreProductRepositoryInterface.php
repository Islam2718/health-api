<?php
namespace App\Domain\Interfaces;

use App\Infrastructure\Persistence\Models\StoreProduct;
use Illuminate\Pagination\LengthAwarePaginator;

interface StoreProductRepositoryInterface
{
    public function getAll(int $storeId, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?StoreProduct;
    public function findByStoreAndMedicine(int $storeId, int $medicineId): ?StoreProduct;
    public function create(array $data): StoreProduct;
    public function update(int $id, array $data): StoreProduct;
    public function delete(int $id): bool;
    public function getStoreProductsWithStock(int $storeId): array;
}