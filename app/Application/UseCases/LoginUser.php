<?php 
// app/Application/UseCases/LoginUser.php

namespace App\Application\UseCases;

use App\Domain\Interfaces\UserRepository;
use Illuminate\Support\Facades\Hash;

class LoginUser
{
    public function __construct(private UserRepository $repo) {}

    public function execute(string $identifier, string $password)
    {
        $user = $this->repo->findByIdentifier($identifier);

        if (!$user) {
            throw new \Exception('User not found');
        }

        if (!Hash::check($password, $user->password)) {
            throw new \Exception('Invalid password');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}