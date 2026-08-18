<?php

namespace App\Application\UseCases;

use App\Application\DTOs\AmbulanceDTO;
use App\Domain\Interfaces\AmbulanceRepositoryInterface;
use App\Infrastructure\Persistence\Models\Ambulance;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateAmbulance
{
    public function __construct(
        private readonly AmbulanceRepositoryInterface $repository
    ) {
    }

    public function execute(
        int $ambulanceId,
        int $userId,
        array $data
    ): Ambulance {
        $ambulance = $this->repository->findOwnedByUser(
            $ambulanceId,
            $userId
        );

        if (!$ambulance) {
            throw new NotFoundHttpException(
                'Ambulance not found.'
            );
        }

        $dto = AmbulanceDTO::fromArray(
            $data,
            $userId
        );

        return $this->repository->update(
            $ambulance,
            $dto
        );
    }
}