<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Interfaces\MedicineRepository;
use App\Domain\Interfaces\MedicineCompanyRepository;
use App\Infrastructure\Persistence\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function publicIndex(Request $request)
    {
        $data = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'company_id' => ['sometimes', 'integer', 'exists:medicine_companies,id'],
            'random' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Medicine::with('company');

        if (!empty($data['search'])) {
            $query->where(function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('generic_name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('weight', 'like', '%' . $data['search'] . '%');
            });
        }

        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }

        if (!empty($data['company_id'])) {
            $query->where('company_id', $data['company_id']);
        }

        if ($request->boolean('random')) {
            $query->inRandomOrder();
        } else {
            $query->latest();
        }

        $medicines = $query->paginate($data['per_page'] ?? 15)->appends($request->query());

        return response()->json([
            'data' => $medicines->items(),
            'meta' => [
                'current_page' => $medicines->currentPage(),
                'per_page' => $medicines->perPage(),
                'total' => $medicines->total(),
                'last_page' => $medicines->lastPage(),
            ],
        ]);
    }

    public function publicShow(Request $request, $id)
    {
        $medicine = Medicine::with('company')->findOrFail($id);

        return response()->json(['data' => $medicine]);
    }

    public function index(Request $request, MedicineRepository $repo)
    {
        return response()->json(['data' => $repo->getAll()]);
    }

    public function store(Request $request, MedicineRepository $repo, MedicineCompanyRepository $companyRepo)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'string', 'max:255'],
            'suggestion_price' => ['nullable', 'numeric'],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'company_id' => ['required', 'integer', 'exists:medicine_companies,id'],
        ]);

        $companyRepo->findById($data['company_id']);

        return response()->json(['data' => $repo->create($data)], 201);
    }

    public function show(Request $request, $id, MedicineRepository $repo)
    {
        return response()->json(['data' => $repo->findById($id)]);
    }

    public function update(Request $request, $id, MedicineRepository $repo)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'generic_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'weight' => ['sometimes', 'nullable', 'string', 'max:255'],
            'suggestion_price' => ['sometimes', 'nullable', 'numeric'],
            'type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'company_id' => ['sometimes', 'nullable', 'integer', 'exists:medicine_companies,id'],
        ]);

        return response()->json(['data' => $repo->update($id, $data)]);
    }

    public function destroy(Request $request, $id, MedicineRepository $repo)
    {
        $repo->delete($id);
        return response()->json(['message' => 'Medicine deleted']);
    }
}
