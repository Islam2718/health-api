<?php
// app/Domain/Interfaces/UserRepository.php

namespace App\Domain\Interfaces;

interface UserRepository
{
    public function findByIdentifier(string $identifier);
    public function create(array $data);
}