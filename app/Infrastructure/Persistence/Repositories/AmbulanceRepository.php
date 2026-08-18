<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\DTOs\AmbulanceDTO;
use App\Domain\Interfaces\AmbulanceRepositoryInterface;
use App\Infrastructure\Persistence\Models\Ambulance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AmbulanceRepository implements AmbulanceRepositoryInterface
{
    public function create(AmbulanceDTO $dto): Ambulance
    {
        return Ambulance::create($dto->toArray());
    }

    public function findById(int $id): ?Ambulance
    {
        return Ambulance::with('user')->find($id);
    }

    public function findOwnedByUser(
        int $id,
        int $userId
    ): ?Ambulance {
        return Ambulance::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->with('user')
            ->first();
    }

    public function update(
        Ambulance $ambulance,
        AmbulanceDTO $dto
    ): Ambulance {
        $ambulance->update(
            array_filter(
                $dto->toArray(),
                fn ($value) => $value !== null
            )
        );

        return $ambulance->fresh('user');
    }

    public function delete(Ambulance $ambulance): bool
    {
        return (bool) $ambulance->delete();
    }

    public function getUserAmbulances(
        int $userId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Ambulance::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function getPublicAmbulances(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Ambulance::query()
            ->where('is_active', true)
            ->with([
                'user:id,name,phone,address,profile_image',
            ]);

        if (!empty($filters['ambulance_type'])) {
            $query->where(
                'ambulance_type',
                $filters['ambulance_type']
            );
        }

        if (!empty($filters['air_conditioning'])) {
            $query->where(
                'air_conditioning',
                $filters['air_conditioning']
            );
        }

        if (!empty($filters['equipment'])) {
            $equipment = $filters['equipment'];

            $query->whereJsonContains(
                'equipment_list',
                $equipment
            );
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('ambulance_type', 'like', "%{$search}%")
                    ->orWhere('manufacturer', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere(
                        'license_plate_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['address'])) {
            $address = $filters['address'];

            $query->whereHas('user', function ($userQuery) use ($address) {
                $userQuery->where(
                    'address',
                    'like',
                    "%{$address}%"
                );
            });
        }

        if (
            isset($filters['random']) &&
            filter_var($filters['random'], FILTER_VALIDATE_BOOLEAN)
        ) {
            $query->inRandomOrder();
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }
}