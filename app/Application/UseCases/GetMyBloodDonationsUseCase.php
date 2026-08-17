<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BloodDonationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetMyBloodDonationsUseCase
{
    public function __construct(
        private readonly BloodDonationRepositoryInterface $repository
    ) {}

    public function execute(
        int $userId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->repository->getByDonor(
            $userId,
            $perPage
        );
    }
}