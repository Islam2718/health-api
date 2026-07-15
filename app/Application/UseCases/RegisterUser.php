<?php 
// app/Application/UseCases/RegisterUser.php

namespace App\Application\UseCases;

use App\Domain\Interfaces\UserRepository;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function __construct(private UserRepository $repo) {}

    public function execute(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        return $this->repo->create($data);
    }
}