<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::where(function ($query) use ($request) {
            $query->where('user_patient_id', $request->user()->id)
                ->orWhere('user_doctor_id', $request->user()->id);
        })->latest()->get();

        return response()->json(['data' => $appointments]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_doctor_id' => ['required', 'exists:users,id'],
            'hospital_id' => ['sometimes', 'nullable', 'exists:hospitals,id'],
            'chamber_id' => ['sometimes', 'nullable', 'exists:chambers,id'],
            'doctor_schedule_id' => ['sometimes', 'nullable', 'exists:doctor_schedules,id'],
            'consultation_fee' => ['sometimes', 'nullable', 'numeric'],
            'discount' => ['sometimes', 'nullable', 'numeric'],
            'appointment_type' => ['sometimes', 'required', 'in:HOSPITAL,CHAMBER,ONLINE'],
            'status' => ['sometimes', 'nullable', 'in:PENDING,APPROVED,REJECTED,CANCELLED,COMPLETED,EXPIRED'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['sometimes', 'nullable', 'date_format:H:i:s'],
        ]);

        $data['user_patient_id'] = $request->user()->id;

        $appointment = Appointment::create($data);

        return response()->json(['data' => $appointment], 201);
    }

    public function show(Request $request, $id)
    {
        $appointment = Appointment::where(function ($query) use ($request) {
            $query->where('user_patient_id', $request->user()->id)
                ->orWhere('user_doctor_id', $request->user()->id);
        })->where('id', $id)->firstOrFail();

        return response()->json(['data' => $appointment]);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::where(function ($query) use ($request) {
            $query->where('user_patient_id', $request->user()->id)
                ->orWhere('user_doctor_id', $request->user()->id);
        })->where('id', $id)->firstOrFail();

        $data = $request->validate([
            'user_doctor_id' => ['sometimes', 'required', 'exists:users,id'],
            'hospital_id' => ['sometimes', 'nullable', 'exists:hospitals,id'],
            'chamber_id' => ['sometimes', 'nullable', 'exists:chambers,id'],
            'doctor_schedule_id' => ['sometimes', 'nullable', 'exists:doctor_schedules,id'],
            'consultation_fee' => ['sometimes', 'nullable', 'numeric'],
            'discount' => ['sometimes', 'nullable', 'numeric'],
            'appointment_type' => ['sometimes', 'in:HOSPITAL,CHAMBER,ONLINE'],
            'status' => ['sometimes', 'in:PENDING,APPROVED,REJECTED,CANCELLED,COMPLETED,EXPIRED'],
            'appointment_date' => ['sometimes', 'date'],
            'appointment_time' => ['sometimes', 'nullable', 'date_format:H:i:s'],
        ]);

        $appointment->update($data);

        return response()->json(['data' => $appointment]);
    }

    public function destroy(Request $request, $id)
    {
        $appointment = Appointment::where(function ($query) use ($request) {
            $query->where('user_patient_id', $request->user()->id)
                ->orWhere('user_doctor_id', $request->user()->id);
        })->where('id', $id)->firstOrFail();

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted']);
    }

    public function upcoming(Request $request)
    {
        $today = now()->toDateString();

        $appointments = Appointment::where(function ($query) use ($request) {
            $query->where('user_patient_id', $request->user()->id)
                ->orWhere('user_doctor_id', $request->user()->id);
        })
            ->whereDate('appointment_date', '>=', $today)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return response()->json(['data' => $appointments]);
    }
}
