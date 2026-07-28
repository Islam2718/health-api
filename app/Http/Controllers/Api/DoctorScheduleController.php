<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => DoctorSchedule::where('user_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'chamber_id' => ['required', 'exists:chambers,id'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i:s'],
            'end_time' => ['nullable', 'date_format:H:i:s'],
            'slot_duration' => ['nullable', 'integer'],
            'max_patients' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['user_id'] = $request->user()->id;

        $schedule = DoctorSchedule::create($data);

        return response()->json(['data' => $schedule], 201);
    }

    public function show(Request $request, $id)
    {
        $schedule = DoctorSchedule::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        return response()->json(['data' => $schedule]);
    }

    public function update(Request $request, $id)
    {
        $schedule = DoctorSchedule::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        $schedule->update($request->validate([
            'chamber_id' => ['nullable', 'exists:chambers,id'],
            'date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i:s'],
            'end_time' => ['nullable', 'date_format:H:i:s'],
            'slot_duration' => ['nullable', 'integer'],
            'max_patients' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]));

        return response()->json(['data' => $schedule]);
    }

    public function destroy(Request $request, $id)
    {
        $schedule = DoctorSchedule::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();
        $schedule->delete();

        return response()->json(['message' => 'Doctor schedule deleted']);
    }
}
