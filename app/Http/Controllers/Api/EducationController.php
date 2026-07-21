<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => Education::where('user_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'institution' => ['nullable', 'string', 'max:255'],
            'degree' => ['nullable', 'string', 'max:255'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'grade' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $data['user_id'] = $request->user()->id;

        $education = Education::create($data);

        return response()->json(['data' => $education], 201);
    }

    public function show(Request $request, $id)
    {
        $education = Education::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        return response()->json(['data' => $education]);
    }

    public function update(Request $request, $id)
    {
        $education = Education::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();
        $education->update($request->validate([
            'institution' => ['nullable', 'string', 'max:255'],
            'degree' => ['nullable', 'string', 'max:255'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'grade' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]));

        return response()->json(['data' => $education]);
    }

    public function destroy(Request $request, $id)
    {
        $education = Education::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();
        $education->delete();

        return response()->json(['message' => 'Education deleted']);
    }
}
