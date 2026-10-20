<?php
namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Interfaces\StockRepositoryInterface;
use App\Infrastructure\Persistence\Models\OrderItem;
use App\Infrastructure\Persistence\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockRepository implements StockRepositoryInterface
{
    public function getAll(int $storeProductId, array $filters = []): LengthAwarePaginator
    {
        $query = Stock::where('store_product_id', $storeProductId);

        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('transaction_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('transaction_date', '<=', $filters['date_to']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('transaction_date', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Stock
    {
        return Stock::find($id);
    }

    public function create(array $data): Stock
    {
        return Stock::create($data);
    }

    public function getProductStockHistory(int $storeProductId): LengthAwarePaginator
    {
        return Stock::where('store_product_id', $storeProductId)
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);
    }

    public function getCurrentStock(int $storeProductId): int
    {
        return Stock::where('store_product_id', $storeProductId)
            ->where('transaction_type', 'purchase')
            ->sum('quantity') - OrderItem::where('store_product_id', $storeProductId)
            ->sum('quantity');
    }

    public function getStockSummary(int $storeId): array
    {
        return DB::table('store_products')
            ->join('stocks', 'store_products.id', '=', 'stocks.store_product_id')
            ->where('store_products.store_id', $storeId)
            ->select(
                'store_products.id as product_id',
                DB::raw('COALESCE(SUM(CASE WHEN stocks.transaction_type = "purchase" THEN stocks.quantity ELSE 0 END), 0) as total_purchased'),
                DB::raw('COALESCE(SUM(CASE WHEN stocks.transaction_type = "sale" THEN stocks.quantity ELSE 0 END), 0) as total_sold'),
                DB::raw('COALESCE(SUM(CASE WHEN stocks.transaction_type = "purchase" THEN stocks.total_price ELSE 0 END), 0) as total_purchase_amount'),
                DB::raw('COALESCE(SUM(CASE WHEN stocks.transaction_type = "sale" THEN stocks.total_price ELSE 0 END), 0) as total_sale_amount')
            )
            ->groupBy('store_products.id')
            ->get()
            ->toArray();
    }
}