<?php
namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Interfaces\StoreRepositoryInterface;
use App\Infrastructure\Persistence\Models\Store;
use Illuminate\Pagination\LengthAwarePaginator;

class StoreRepository implements StoreRepositoryInterface
{
    public function getAll(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Store::where('user_id', $userId);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('store_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('store_address', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('trade_license_no', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Store
    {
        return Store::find($id);
    }

    public function findByUserId(int $userId): ?Store
    {
        return Store::where('user_id', $userId)->first();
    }

    public function create(array $data): Store
    {
        return Store::create($data);
    }

    public function update(int $id, array $data): Store
    {
        $store = Store::findOrFail($id);
        $store->update($data);
        return $store->fresh();
    }

    public function delete(int $id): bool
    {
        $store = Store::findOrFail($id);
        return $store->delete();
    }

    public function getByLicenseNo(string $licenseNo): ?Store
    {
        return Store::where('trade_license_no', $licenseNo)->first();
    }
}