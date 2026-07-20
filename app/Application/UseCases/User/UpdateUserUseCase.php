<?php

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepository;
use Illuminate\Support\Facades\Hash;

class UpdateUserUseCase
{
    public function __construct(private UserRepository $repo) {}

    public function execute($id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->repo->update($id, $data);
    }
}