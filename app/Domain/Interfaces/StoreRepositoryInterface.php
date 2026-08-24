<?php
namespace App\Domain\Interfaces;

use App\Infrastructure\Persistence\Models\Store;
use Illuminate\Pagination\LengthAwarePaginator;

interface StoreRepositoryInterface
{
    public function getAll(int $userId, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Store;
    public function findByUserId(int $userId): ?Store;
    public function create(array $data): Store;
    public function update(int $id, array $data): Store;
    public function delete(int $id): bool;
    public function getByLicenseNo(string $licenseNo): ?Store;
}