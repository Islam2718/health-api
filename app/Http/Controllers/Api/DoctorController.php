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
