<?php

namespace App\Application\UseCases;

use App\Application\DTOs\CreateBloodDonationDTO;
use App\Domain\Interfaces\BloodDonationRepositoryInterface;
use App\Infrastructure\Persistence\Models\BloodDonation;

class CreateBloodDonationUseCase
{
    public function __construct(
        private readonly BloodDonationRepositoryInterface $repository
    ) {}

    public function execute(
        CreateBloodDonationDTO $dto
    ): BloodDonation {
        return $this->repository->create($dto);
    }
}