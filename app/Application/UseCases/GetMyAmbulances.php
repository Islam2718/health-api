<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\AmbulanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetMyAmbulances
{
    public function __construct(
        private readonly AmbulanceRepositoryInterface $repository
    ) {
    }

    public function execute(
        int $userId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->repository->getUserAmbulances(
            $userId,
            $perPage
        );
    }
}