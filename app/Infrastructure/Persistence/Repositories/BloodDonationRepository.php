<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\DTOs\CreateBloodDonationDTO;
use App\Domain\Interfaces\BloodDonationRepositoryInterface;
use App\Infrastructure\Persistence\Models\BloodDonation;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BloodDonationRepository implements BloodDonationRepositoryInterface
{
    public function create(
        CreateBloodDonationDTO $dto
    ): BloodDonation {
        return BloodDonation::create([
            'donor_user_id' => $dto->donorUserId,
            'patient_name' => $dto->patientName,
            'patient_gender' => $dto->patientGender,
            'patient_disease' => $dto->patientDisease,
            'patient_blood_group' => $dto->patientBloodGroup,
            'donation_date' => $dto->donationDate,
            'hospital_name' => $dto->hospitalName,
            'hospital_address' => $dto->hospitalAddress,
            'units' => $dto->units,
            'notes' => $dto->notes,
        ]);
    }

    public function findById(int $id): ?BloodDonation
    {
        return BloodDonation::with('donor')
            ->find($id);
    }

    public function getByDonor(
        int $donorUserId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return BloodDonation::query()
            ->where('donor_user_id', $donorUserId)
            ->latest('donation_date')
            ->paginate($perPage);
    }

    public function getDonors(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = User::query()
            ->where('donor_interest', true);

        if (!empty($filters['blood_group'])) {
            $query->where(
                'blood_group',
                $filters['blood_group']
            );
        }

        if (!empty($filters['gender'])) {
            $query->where(
                'gender',
                $filters['gender']
            );
        }

        if (!empty($filters['address'])) {
            $query->where(
                'address',
                'like',
                '%' . $filters['address'] . '%'
            );
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query
            ->latest()
            ->paginate($perPage);
    }
}