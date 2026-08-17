<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BloodDonationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetBloodDonorsUseCase
{
    public function __construct(
        private readonly BloodDonationRepositoryInterface $repository
    ) {}

    public function execute(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->repository->getDonors(
            $filters,
            $perPage
        );
    }
}