<?php

namespace App\Application\DTOs;

class UpdateDonorInterestDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly bool $donorInterest,
    ) {}
}