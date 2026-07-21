<?php

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepository;

class CreateUserUseCase
{
    public function __construct(private UserRepository $repo) {}

    public function execute(array $data)
    {
        if (!isset($data['type']) || empty($data['type'])) {
            $data['type'] = 'USER';
        }

        return $this->repo->create($data);
    }
}
