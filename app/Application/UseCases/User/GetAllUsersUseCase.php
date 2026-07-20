<?php

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepository;
use Illuminate\Support\Facades\Hash;

class GetAllUsersUseCase
{
    public function __construct(private UserRepository $repo) {}

    public function execute()
    {
        return $this->repo->getAll();
    }
}