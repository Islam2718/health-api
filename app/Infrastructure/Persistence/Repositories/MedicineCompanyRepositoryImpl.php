<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Interfaces\MedicineCompanyRepository;
use App\Infrastructure\Persistence\Models\MedicineCompany;

class MedicineCompanyRepositoryImpl implements MedicineCompanyRepository
{
    public function create(array $data)
    {
        return MedicineCompany::create($data);
    }

    public function getAll()
    {
        return MedicineCompany::latest()->get();
    }

    public function findById($id)
    {
        return MedicineCompany::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $company = $this->findById($id);
        $company->update($data);
        return $company;
    }

    public function delete($id)
    {
        $company = $this->findById($id);
        return $company->delete();
    }
}
