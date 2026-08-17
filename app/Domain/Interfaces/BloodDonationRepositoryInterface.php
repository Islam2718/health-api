<?php

namespace App\Domain\Interfaces;

use App\Application\DTOs\CreateBloodDonationDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BloodDonationRepositoryInterface
{
    public function create(
        CreateBloodDonationDTO $dto
    ): mixed;

    public function findById(
        int $id
    ): mixed;

    public function getByDonor(
        int $donorUserId,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getDonors(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;
}