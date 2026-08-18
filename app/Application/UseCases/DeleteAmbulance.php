<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\AmbulanceRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteAmbulance
{
    public function __construct(
        private readonly AmbulanceRepositoryInterface $repository
    ) {
    }

    public function execute(
        int $ambulanceId,
        int $userId
    ): void {
        $ambulance = $this->repository->findOwnedByUser(
            $ambulanceId,
            $userId
        );

        if (!$ambulance) {
            throw new NotFoundHttpException(
                'Ambulance not found.'
            );
        }

        $this->repository->delete($ambulance);
    }
}