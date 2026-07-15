<?php 
// app/Infrastructure/Persistence/Repositories/UserRepositoryImpl.php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Interfaces\UserRepository;
use App\Infrastructure\Persistence\Models\User;

class UserRepositoryImpl implements UserRepository
{
    public function findByIdentifier(string $identifier)
    {
        return User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->orWhere('phone', $identifier)
            ->first();
    }

    public function create(array $data)
    {
        return User::create($data);
    }
}