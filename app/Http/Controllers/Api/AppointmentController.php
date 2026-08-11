<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * List appointments assigned to the authenticated doctor.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_patient_id": 2,
     *       "user_doctor_id": 3,
     *       "hospital_id": 4,
     *       "chamber_id": 5,
     *       "doctor_schedule_id": null,
     *       "consultation_fee": "500.00",
     *       "discount": "0.00",
     *       "appointment_type": "HOSPITAL",
     *       "status": "PENDING",
     *       "appointment_date": "2026-08-08",
     *       "appointment_time": "10:00:00",
     *       "created_at": "2026-08-08T08:00:00.000000Z",
     *       "updated_at": "2026-08-08T08:00:00.000000Z",
     *       "user_patient": {
     *         "id": 2,
     *         "name": "Patient User",
     *         "email": "patient@example.com"
     *       },
     *       "user_doctor": {
     *         "id": 3,
     *         "name": "Doctor User",
     *         "email": "doctor@example.com"
     *       },
     *       "hospital": {
     *         "id": 4,
     *         "name": "City Hospital"
     *       },
     *       "chamber": {
     *         "id": 5,
     *         "name": "Room 101"
     *       }
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $appointments = Appointment::with(['user_patient', 'user_doctor', 'hospital', 'chamber'])
            ->where(function ($query) use ($request) {
                $query->where('user_doctor_id', $request->user()->id);
                    // ->orWhere('user_patient_id', $request->user()->id);
            })->latest()->get();

        return response()->json(['data' => $appointments]);
    }

    /**
     * List patient appointments for the authenticated user.
     */
    public function myAppointments(Request $request)
    {
        $appointments = Appointment::with(['user_patient', 'user_doctor', 'hospital', 'chamber'])
            ->where('user_patient_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['data' => $appointments]);
    }

    /**
     * Create a new appointment for the authenticated user.
     *
     * @bodyParam user_doctor_id int optional The doctor user ID. Defaults to authenticated user ID if omitted.
     * @bodyParam user_patient_id int optional The patient user ID. Defaults to authenticated user ID if omitted.
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user_patient_id": 2,
     *     "user_doctor_id": 3,
     *     "hospital_id": 4,
     *     "chamber_id": 5,
     *     "doctor_schedule_id": null,
     *     "consultation_fee": "500.00",
     *     "discount": "0.00",
     *     "appointment_type": "HOSPITAL",
     *     "status": "PENDING",
     *     "appointment_date": "2026-08-08",
     *     "appointment_time": "10:00:00",
     *     "created_at": "2026-08-08T08:00:00.000000Z",
     *     "updated_at": "2026-08-08T08:00:00.000000Z",
     *     "user_patient": {
     *       "id": 2,
     *       "name": "Patient User",
     *       "email": "patient@example.com"
     *     },
     *     "user_doctor": {
     *       "id": 3,
     *       "name": "Doctor User",
     *       "email": "doctor@example.com"
     *     },
     *     "hospital": {
     *       "id": 4,
     *       "name": "City Hospital"
     *     },
     *     "chamber": {
     *       "id": 5,
     *       "name": "Room 101"
     *     }
     *   }
     * }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_doctor_id' => ['sometimes', 'exists:users,id'],
            'user_patient_id' => ['sometimes', 'exists:users,id'],
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

        $data['user_doctor_id'] = $request->input('user_doctor_id') ?? $request->user()->id;
        $data['user_patient_id'] = $request->input('user_patient_id') ?? $request->user()->id;

        $appointment = Appointment::create($data);
        $appointment->load(['userPatient', 'userDoctor', 'hospital', 'chamber']);

        return response()->json(['data' => $appointment], 201);
    }

    /**
     * Get a single appointment for the authenticated user.
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_patient_id": 2,
     *     "user_doctor_id": 3,
     *     "hospital_id": 4,
     *     "chamber_id": 5,
     *     "doctor_schedule_id": null,
     *     "consultation_fee": "500.00",
     *     "discount": "0.00",
     *     "appointment_type": "HOSPITAL",
     *     "status": "PENDING",
     *     "appointment_date": "2026-08-08",
     *     "appointment_time": "10:00:00",
     *     "created_at": "2026-08-08T08:00:00.000000Z",
     *     "updated_at": "2026-08-08T08:00:00.000000Z",
     *     "user_patient": {
     *       "id": 2,
     *       "name": "Patient User",
     *       "email": "patient@example.com"
     *     },
     *     "user_doctor": {
     *       "id": 3,
     *       "name": "Doctor User",
     *       "email": "doctor@example.com"
     *     },
     *     "hospital": {
     *       "id": 4,
     *       "name": "City Hospital"
     *     },
     *     "chamber": {
     *       "id": 5,
     *       "name": "Room 101"
     *     }
     *   }
     * }
     */
    public function show(Request $request, $id)
    {
        $appointment = Appointment::with(['userPatient', 'userDoctor', 'hospital', 'chamber'])
            ->where(function ($query) use ($request) {
                $query->where('user_patient_id', $request->user()->id)
                    ->orWhere('user_doctor_id', $request->user()->id);
            })->where('id', $id)->firstOrFail();

        return response()->json(['data' => $appointment]);
    }

    /**
     * Update an appointment for the authenticated user.
     *
     * @bodyParam user_doctor_id int optional The doctor user ID. Defaults to authenticated user ID if omitted.
     * @bodyParam user_patient_id int optional The patient user ID. Defaults to authenticated user ID if omitted.
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_patient_id": 2,
     *     "user_doctor_id": 3,
     *     "hospital_id": 4,
     *     "chamber_id": 5,
     *     "doctor_schedule_id": null,
     *     "consultation_fee": "500.00",
     *     "discount": "0.00",
     *     "appointment_type": "HOSPITAL",
     *     "status": "APPROVED",
     *     "appointment_date": "2026-08-08",
     *     "appointment_time": "10:00:00",
     *     "created_at": "2026-08-08T08:00:00.000000Z",
     *     "updated_at": "2026-08-08T08:30:00.000000Z",
     *     "user_patient": {
     *       "id": 2,
     *       "name": "Patient User",
     *       "email": "patient@example.com"
     *     },
     *     "user_doctor": {
     *       "id": 3,
     *       "name": "Doctor User",
     *       "email": "doctor@example.com"
     *     },
     *     "hospital": {
     *       "id": 4,
     *       "name": "City Hospital"
     *     },
     *     "chamber": {
     *       "id": 5,
     *       "name": "Room 101"
     *     }
     *   }
     * }
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::where(function ($query) use ($request) {
            $query->where('user_patient_id', $request->user()->id)
                ->orWhere('user_doctor_id', $request->user()->id);
        })->where('id', $id)->firstOrFail();

        $data = $request->validate([
            'user_doctor_id' => ['sometimes', 'exists:users,id'],
            'user_patient_id' => ['sometimes', 'exists:users,id'],
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

        if (!array_key_exists('user_doctor_id', $data)) {
            $data['user_doctor_id'] = $request->user()->id;
        }

        if (!array_key_exists('user_patient_id', $data)) {
            $data['user_patient_id'] = $request->user()->id;
        }

        $appointment->update($data);
        $appointment->load(['userPatient', 'userDoctor', 'hospital', 'chamber']);

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

    /**
     * Get upcoming appointments assigned to the authenticated doctor.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_patient_id": 2,
     *       "user_doctor_id": 3,
     *       "hospital_id": 4,
     *       "chamber_id": 5,
     *       "doctor_schedule_id": null,
     *       "consultation_fee": "500.00",
     *       "discount": "0.00",
     *       "appointment_type": "HOSPITAL",
     *       "status": "PENDING",
     *       "appointment_date": "2026-08-09",
     *       "appointment_time": "11:00:00",
     *       "created_at": "2026-08-08T08:00:00.000000Z",
     *       "updated_at": "2026-08-08T08:00:00.000000Z",
     *       "user_patient": {
     *         "id": 2,
     *         "name": "Patient User",
     *         "email": "patient@example.com"
     *       },
     *       "user_doctor": {
     *         "id": 3,
     *         "name": "Doctor User",
     *         "email": "doctor@example.com"
     *       },
     *       "hospital": {
     *         "id": 4,
     *         "name": "City Hospital"
     *       },
     *       "chamber": {
     *         "id": 5,
     *         "name": "Room 101"
     *       }
     *     }
     *   ]
     * }
     */
    public function upcoming(Request $request)
    {
        $today = now()->toDateString();

        $appointments = Appointment::with(['userPatient', 'userDoctor', 'hospital', 'chamber'])
            ->where(function ($query) use ($request) {
                $query->where('user_doctor_id', $request->user()->id);
                    // where('user_patient_id', $request->user()->id)
                    
            })
            ->whereDate('appointment_date', '>=', $today)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return response()->json(['data' => $appointments]);
    }
}
