<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\ProfessionalExperience;
use Illuminate\Http\Request;

class ProfessionalExperienceController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => ProfessionalExperience::where('user_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'job_title' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_current' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $data['user_id'] = $request->user()->id;

        $experience = ProfessionalExperience::create($data);

        return response()->json(['data' => $experience], 201);
    }

    public function show(Request $request, $id)
    {
        $experience = ProfessionalExperience::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        return response()->json(['data' => $experience]);
    }

    public function update(Request $request, $id)
    {
        $experience = ProfessionalExperience::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();
        $experience->update($request->validate([
            'job_title' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_current' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]));

        return response()->json(['data' => $experience]);
    }

    public function destroy(Request $request, $id)
    {
        $experience = ProfessionalExperience::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();
        $experience->delete();

        return response()->json(['message' => 'Professional experience deleted']);
    }
}
