<?php

namespace App\Application\DTOs;

class CreateBloodDonationDTO
{
    public function __construct(
        public readonly int $donorUserId,
        public readonly string $patientName,
        public readonly ?string $patientGender,
        public readonly ?string $patientDisease,
        public readonly ?string $patientBloodGroup,
        public readonly string $donationDate,
        public readonly ?string $hospitalName,
        public readonly ?string $hospitalAddress,
        public readonly int $units,
        public readonly ?string $notes,
    ) {}
}