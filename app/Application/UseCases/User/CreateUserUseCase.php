<?php

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepository;
use Illuminate\Support\Facades\Hash;

class CreateUserUseCase
{
    public function __construct(private UserRepository $repo) {}

    public function execute(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->repo->create($data);
    }
}
