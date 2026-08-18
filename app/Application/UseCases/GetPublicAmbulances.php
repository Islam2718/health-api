<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\AmbulanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetPublicAmbulances
{
    public function __construct(
        private readonly AmbulanceRepositoryInterface $repository
    ) {
    }

    public function execute(
        array $filters = []
    ): LengthAwarePaginator {
        $perPage = min(
            max((int) ($filters['per_page'] ?? 15), 1),
            100
        );

        return $this->repository->getPublicAmbulances(
            $filters,
            $perPage
        );
    }
}