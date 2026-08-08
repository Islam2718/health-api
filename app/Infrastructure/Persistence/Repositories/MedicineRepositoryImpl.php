<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Interfaces\MedicineRepository;
use App\Infrastructure\Persistence\Models\Medicine;

class MedicineRepositoryImpl implements MedicineRepository
{
    public function create(array $data)
    {
        return Medicine::create($data);
    }

    public function getAll()
    {
        return Medicine::with('company')->latest()->get();
    }

    public function findById($id)
    {
        return Medicine::with('company')->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $medicine = $this->findById($id);
        $medicine->update($data);
        return $medicine;
    }

    public function delete($id)
    {
        $medicine = $this->findById($id);
        return $medicine->delete();
    }
}
