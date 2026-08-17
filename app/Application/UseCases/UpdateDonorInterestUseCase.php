<?php

namespace App\Application\UseCases;

use App\Application\DTOs\UpdateDonorInterestDTO;
use App\Infrastructure\Persistence\Models\User;

class UpdateDonorInterestUseCase
{
    public function execute(
        UpdateDonorInterestDTO $dto
    ): User {
        $user = User::findOrFail($dto->userId);

        $user->update([
            'donor_interest' => $dto->donorInterest,
        ]);

        return $user->fresh();
    }
}