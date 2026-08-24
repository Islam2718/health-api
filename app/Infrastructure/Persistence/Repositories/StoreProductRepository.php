<?php
namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Interfaces\StoreProductRepositoryInterface;
use App\Infrastructure\Persistence\Models\StoreProduct;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StoreProductRepository implements StoreProductRepositoryInterface
{
    public function getAll(int $storeId, array $filters = []): LengthAwarePaginator
    {
        $query = StoreProduct::with(['medicine', 'stocks'])
            ->where('store_id', $storeId);

        if (!empty($filters['search'])) {
            $query->whereHas('medicine', function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('generic_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['low_stock'])) {
            $query->whereRaw('(SELECT COALESCE(SUM(CASE WHEN transaction_type = "purchase" THEN quantity ELSE -quantity END), 0) 
                FROM stocks WHERE store_products.id = stocks.store_product_id) <= minimum_stock');
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?StoreProduct
    {
        return StoreProduct::with(['medicine', 'stocks'])->find($id);
    }

    public function findByStoreAndMedicine(int $storeId, int $medicineId): ?StoreProduct
    {
        return StoreProduct::where('store_id', $storeId)
            ->where('medicine_id', $medicineId)
            ->first();
    }

    public function create(array $data): StoreProduct
    {
        return StoreProduct::create($data);
    }

    public function update(int $id, array $data): StoreProduct
    {
        $storeProduct = StoreProduct::findOrFail($id);
        $storeProduct->update($data);
        return $storeProduct->fresh();
    }

    public function delete(int $id): bool
    {
        $storeProduct = StoreProduct::findOrFail($id);
        return $storeProduct->delete();
    }

    public function getStoreProductsWithStock(int $storeId): array
    {
        return StoreProduct::with(['medicine', 'stocks'])
            ->where('store_id', $storeId)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'medicine_name' => $product->medicine->name,
                    'buy_price' => $product->buy_price,
                    'sale_price' => $product->sale_price,
                    'wholesale_price' => $product->wholesale_price,
                    'minimum_stock' => $product->minimum_stock,
                    'current_stock' => $product->current_stock,
                ];
            })
            ->toArray();
    }
}