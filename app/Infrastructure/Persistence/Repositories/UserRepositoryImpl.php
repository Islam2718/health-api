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

    public function getAll()
    {
        return User::latest()->get();
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = $this->findById($id);
        return $user->delete();
    }
}
