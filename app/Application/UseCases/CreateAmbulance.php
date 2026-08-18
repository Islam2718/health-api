<?php

namespace App\Application\UseCases;

use App\Application\DTOs\AmbulanceDTO;
use App\Domain\Interfaces\AmbulanceRepositoryInterface;
use App\Infrastructure\Persistence\Models\Ambulance;

class CreateAmbulance
{
    public function __construct(
        private readonly AmbulanceRepositoryInterface $repository
    ) {
    }

    public function execute(
        int $userId,
        array $data
    ): Ambulance {
        $dto = AmbulanceDTO::fromArray(
            $data,
            $userId
        );

        return $this->repository->create($dto);
    }
}