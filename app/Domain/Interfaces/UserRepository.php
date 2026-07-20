<?php
// app/Domain/Interfaces/UserRepository.php

namespace App\Domain\Interfaces;

interface UserRepository
{
    public function findByIdentifier(string $identifier);
    public function create(array $data);

    public function getAll();
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
}