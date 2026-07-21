<?php

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepository;

class UpdateUserUseCase
{
    public function __construct(private UserRepository $repo) {}

    public function execute($id, array $data)
    {
        return $this->repo->update($id, $data);
    }
}