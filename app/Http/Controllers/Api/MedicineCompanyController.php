<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Interfaces\MedicineCompanyRepository;
use App\Infrastructure\Persistence\Models\MedicineCompany;
use Illuminate\Http\Request;

class MedicineCompanyController extends Controller
{
    public function publicIndex(Request $request)
    {
        $data = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'random' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $query = MedicineCompany::with('medicines');

        if (!empty($data['search'])) {
            $query->where(function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('address', 'like', '%' . $data['search'] . '%')
                    ->orWhere('license_number', 'like', '%' . $data['search'] . '%');
            });
        }

        if ($request->boolean('random')) { 
            $query->inRandomOrder();
        } else {
            $query->latest();
        }

        $companies = $query->paginate($data['per_page'] ?? 15)->appends($request->query());

        return response()->json([
            'data' => $companies->items(),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
                'last_page' => $companies->lastPage(),
            ],
        ]);
    }

    public function publicShow(Request $request, $id)
    {
        $company = MedicineCompany::with('medicines')->findOrFail($id);

        return response()->json(['data' => $company]);
    }

    public function index(Request $request, MedicineCompanyRepository $repo)
    {
        return response()->json(['data' => $repo->getAll()]);
    }

    public function store(Request $request, MedicineCompanyRepository $repo)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string'],
        ]);

        return response()->json(['data' => $repo->create($data)], 201);
    }

    public function show(Request $request, $id, MedicineCompanyRepository $repo)
    {
        return response()->json(['data' => $repo->findById($id)]);
    }

    public function update(Request $request, $id, MedicineCompanyRepository $repo)
    {
        $company = $repo->findById($id);

        $data = $request->validate([
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'logo' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string'],
            'license_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'about' => ['sometimes', 'nullable', 'string'],
        ]);

        return response()->json(['data' => $repo->update($company->id, $data)]);
    }

    public function destroy(Request $request, $id, MedicineCompanyRepository $repo)
    {
        $repo->delete($id);
        return response()->json(['message' => 'Medicine company deleted']);
    }
}
