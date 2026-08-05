<?php

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepository;

class FindUserByPhoneUseCase
{
    public function __construct(private UserRepository $repo) {}

    public function execute(string $phone)
    {
        return $this->repo->findByIdentifier($phone);
    }
}
