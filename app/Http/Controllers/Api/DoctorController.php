<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $doctor = Doctor::where('user_id', $request->user()->id)->first();

        return response()->json([
            'data' => $doctor,
        ]);
    }
 
    public function publicShow(Request $request, $id)
    {
        $doctor = Doctor::with(['user', 'chambers.doctorSchedules' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('date')
                ->orderBy('start_time');
        }])->where('id', $id)->firstOrFail();

        return response()->json(['data' => $doctor]);
    }

    public function publicIndex(Request $request)
    {
        $data = $request->validate([
            'designation' => ['sometimes', 'string', 'max:255'],
            'department' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'search' => ['sometimes', 'string', 'max:255'],
            'random' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Doctor::with('user')->where('is_active', true);

        if (!empty($data['designation'])) {
            $query->where('title', 'like', '%' . $data['designation'] . '%');
        }

        if (!empty($data['department'])) {
            $query->where('specialization', 'like', '%' . $data['department'] . '%');
        }

        if (!empty($data['address'])) {
            $query->whereHas('user', function ($query) use ($data) {
                $query->where('address', 'like', '%' . $data['address'] . '%');
            });
        }

        if (!empty($data['search'])) {
            $query->where(function ($query) use ($data) {
                $query->where('title', 'like', '%' . $data['search'] . '%')
                    ->orWhere('specialization', 'like', '%' . $data['search'] . '%')
                    ->orWhereHas('user', function ($query) use ($data) {
                        $query->where('name', 'like', '%' . $data['search'] . '%')
                            ->orWhere('address', 'like', '%' . $data['search'] . '%');
                    });
            });
        }

        if ($request->boolean('random')) {
            $query->inRandomOrder();
        } else {
            $query->latest();
        }

        $doctors = $query->paginate($data['per_page'] ?? 15)->appends($request->query());

        return response()->json([
            'data' => $doctors->items(),
            'meta' => [
                'current_page' => $doctors->currentPage(),
                'per_page' => $doctors->perPage(),
                'total' => $doctors->total(),
                'last_page' => $doctors->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255', 'unique:doctors,license_number'],
            'bio' => ['nullable', 'string'],
        ]);

        $data['user_id'] = $request->user()->id;

        $doctor = Doctor::create($data);
        // user type update: USER,DOCTOR
        $user = $request->user();
        $user->type = 'USER,DOCTOR';
        $user->save();

        return response()->json([
            'data' => $doctor,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $doctor = Doctor::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        return response()->json(['data' => $doctor]);
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255', 'unique:doctors,license_number,' . $doctor->id],
            'bio' => ['nullable', 'string'],
        ]);

        $doctor->update($data);

        return response()->json(['data' => $doctor]);
    }

    public function destroy(Request $request, $id)
    {
        $doctor = Doctor::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();
        $doctor->delete();

        return response()->json(['message' => 'Doctor profile deleted']);
    }
}
