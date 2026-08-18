<?php

namespace App\Domain\Interfaces;

use App\Application\DTOs\AmbulanceDTO;
use App\Infrastructure\Persistence\Models\Ambulance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AmbulanceRepositoryInterface
{
    public function create(AmbulanceDTO $dto): Ambulance;

    public function findById(int $id): ?Ambulance;

    public function findOwnedByUser(
        int $id,
        int $userId
    ): ?Ambulance;

    public function update(
        Ambulance $ambulance,
        AmbulanceDTO $dto
    ): Ambulance;

    public function delete(Ambulance $ambulance): bool;

    public function getUserAmbulances(
        int $userId,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getPublicAmbulances(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;
}