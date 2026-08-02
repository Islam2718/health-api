<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => Hospital::where('user_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['user_id'] = $request->user()->id;

        $hospital = Hospital::create($data);

        return response()->json(['data' => $hospital], 201);
    }

    public function show(Request $request, $id)
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        return response()->json(['data' => $hospital]);
    }

    public function update(Request $request, $id)
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        $hospital->update($request->validate([
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'license_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'logo' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric'],
            'longitude' => ['sometimes', 'nullable', 'numeric'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
        ]));

        return response()->json(['data' => $hospital]);
    }

    public function destroy(Request $request, $id)
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();
        $hospital->delete();

        return response()->json(['message' => 'Hospital deleted']);
    }
}
