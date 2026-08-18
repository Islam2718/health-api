<?php

namespace App\Http\Controllers\Api;

use App\Application\DTOs\CreateBloodDonationDTO;
use App\Application\DTOs\UpdateDonorInterestDTO;
use App\Application\UseCases\CreateBloodDonationUseCase;
use App\Application\UseCases\GetBloodDonorsUseCase;
use App\Application\UseCases\GetMyBloodDonationsUseCase;
use App\Application\UseCases\UpdateDonorInterestUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodDonationRequest;
use App\Http\Requests\UpdateDonorInterestRequest;
use App\Http\Resources\BloodDonationResource;
use App\Http\Resources\BloodDonorResource;
use Illuminate\Http\Request;
use App\Infrastructure\Persistence\Models\User;

class BloodDonorController extends Controller
{
    public function updateInterest(
        UpdateDonorInterestRequest $request,
        UpdateDonorInterestUseCase $useCase
    ) {
        $user = $useCase->execute(
            new UpdateDonorInterestDTO(
                userId: auth()->id(),
                donorInterest: $request->boolean('donor_interest')
            )
        );

        return response()->json([
            'message' => 'Donor interest updated successfully.',
            'data' => [
                'donor_interest' => (bool) $user->donor_interest,
            ],
        ]);
    }

    public function index(
        Request $request,
        GetBloodDonorsUseCase $useCase
    ) {
        $filters = $request->only([
            'blood_group',
            'gender',
            'address',
            'search',
        ]);

        $perPage = min(
            (int) $request->input('per_page', 15),
            100
        );

        $donors = $useCase->execute(
            $filters,
            $perPage
        );

        return BloodDonorResource::collection($donors);
    }

    public function store(
        StoreBloodDonationRequest $request,
        CreateBloodDonationUseCase $useCase
    ) {
        $donation = $useCase->execute(
            new CreateBloodDonationDTO(
                donorUserId: auth()->id(),
                patientName: $request->string('patient_name')->toString(),
                patientGender: $request->input('patient_gender'),
                patientDisease: $request->input('patient_disease'),
                patientBloodGroup: $request->input('patient_blood_group'),
                donationDate: $request->input('donation_date'),
                hospitalName: $request->input('hospital_name'),
                hospitalAddress: $request->input('hospital_address'),
                units: (int) $request->input('units', 1),
                notes: $request->input('notes'),
            )
        );

        return (new BloodDonationResource($donation))
            ->response()
            ->setStatusCode(201);
    }

    public function myDonations(
        Request $request,
        GetMyBloodDonationsUseCase $useCase
    ) {
        $perPage = min(
            (int) $request->input('per_page', 15),
            100
        );

        $donations = $useCase->execute(
            auth()->id(),
            $perPage
        );

        return BloodDonationResource::collection($donations);
    }

    public function publicIndex(Request $request)
    {
        // dd('here..'); die();
        $query = User::query()
            ->where('donor_interest', true);

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('patient_gender')) {
            $query->where('patient_gender', $request->gender);
        }

        if ($request->filled('address')) {
            $query->where('address', 'like', '%' . $request->address . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $donors = $query->latest()->paginate(
            $request->integer('per_page', 15)
        );

        return BloodDonorResource::collection($donors);
    }
    public function publicShow(int $id)
    {
        $donor = User::query()
            ->where('donor_interest', true)
            ->findOrFail($id);

        return new BloodDonorResource($donor);
    }
}
