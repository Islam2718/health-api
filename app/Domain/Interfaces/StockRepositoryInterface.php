<?php
namespace App\Domain\Interfaces;

use App\Infrastructure\Persistence\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockRepositoryInterface
{
    public function getAll(int $storeProductId, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Stock;
    public function create(array $data): Stock;
    public function getProductStockHistory(int $storeProductId): LengthAwarePaginator;
    public function getCurrentStock(int $storeProductId): int;
    public function getStockSummary(int $storeId): array;
}