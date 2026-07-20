<?php

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepository;

class GetUserUseCase
{
    public function __construct(private UserRepository $repo) {}

    public function execute($id)
    {
        return $this->repo->findById($id);
    }
}
