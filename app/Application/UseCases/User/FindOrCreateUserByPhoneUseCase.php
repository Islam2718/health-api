<?php

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepository;
use Illuminate\Support\Str;

class FindOrCreateUserByPhoneUseCase
{
    public function __construct(private UserRepository $repo) {}

    public function execute(string $phone, array $data)
    {
        $user = $this->repo->findByIdentifier($phone);

        if ($user) {
            return $user;
        }

        $userData = [
            'phone' => $phone,
            'name' => $data['name'] ?? 'Patient ' . $phone,
            'password' => $data['password'] ?? Str::random(12),
            'type' => $data['type'] ?? 'USER',
            'email' => $data['email'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address' => $data['address'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
        ];

        return $this->repo->create($userData);
    }
}
