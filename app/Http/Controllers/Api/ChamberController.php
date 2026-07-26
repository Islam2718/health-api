<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\Chamber;
use Illuminate\Http\Request;

class ChamberController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => Chamber::where('user_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'consultation_fee' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['user_id'] = $request->user()->id;

        $chamber = Chamber::create($data);

        return response()->json(['data' => $chamber], 201);
    }

    public function show(Request $request, $id)
    {
        $chamber = Chamber::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        return response()->json(['data' => $chamber]);
    }

    public function update(Request $request, $id)
    {
        $chamber = Chamber::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        $chamber->update($request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'consultation_fee' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ]));

        return response()->json(['data' => $chamber]);
    }

    public function destroy(Request $request, $id)
    {
        $chamber = Chamber::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();
        $chamber->delete();

        return response()->json(['message' => 'Chamber deleted']);
    }
}
