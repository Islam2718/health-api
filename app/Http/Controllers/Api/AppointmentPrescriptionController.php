<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\Appointment;
use App\Infrastructure\Persistence\Models\AppointmentPrescription;
use Illuminate\Http\Request;

class AppointmentPrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $prescriptions = AppointmentPrescription::where(function ($query) use ($request) {
            $query->where('doctor_user_id', $request->user()->id)
                ->orWhere('patient_user_id', $request->user()->id);
        })->latest()->get();

        return response()->json(['data' => $prescriptions]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'doctor_user_id' => ['required', 'exists:users,id'],
            'patient_user_id' => ['required', 'exists:users,id'],
            'schedule_id' => ['sometimes', 'nullable', 'exists:doctor_schedules,id'],
            'chamber_id' => ['sometimes', 'nullable', 'exists:chambers,id'],
            'appointment_type' => ['required', 'in:HOSPITAL,CHAMBER,ONLINE'],
            'blood_pressure_systolic' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:300'],
            'blood_pressure_diastolic' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:200'],
            'pulse' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:250'],
            'is_smoking' => ['sometimes', 'boolean'],
            'sugar_level' => ['sometimes', 'nullable', 'string', 'max:50'],
            'symptoms' => ['sometimes', 'nullable', 'string'],
            'diagnosis' => ['sometimes', 'nullable', 'string'],
            'medicines' => ['required', 'array'],
            'medicines.*.name' => ['required', 'string', 'max:255'],
            'medicines.*.dose' => ['sometimes', 'nullable', 'string', 'max:255'],
            'medicines.*.schedule' => ['sometimes', 'nullable', 'string', 'max:50'],
            'medicines.*.duration' => ['sometimes', 'nullable', 'string', 'max:100'],
            'medicines.*.notes' => ['sometimes', 'nullable', 'string'],
            'prescription_date' => ['sometimes', 'nullable', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $appointment = Appointment::findOrFail($data['appointment_id']);

        if ($appointment->user_doctor_id !== $data['doctor_user_id'] || $appointment->user_patient_id !== $data['patient_user_id']) {
            return response()->json(['message' => 'Appointment doctor and patient must match prescription values'], 422);
        }

        $data['prescription_date'] = $data['prescription_date'] ?? now()->toDateString();

        $prescription = AppointmentPrescription::create($data);

        return response()->json(['data' => $prescription], 201);
    }

    public function show(Request $request, $id)
    {
        $prescription = AppointmentPrescription::where(function ($query) use ($request) {
            $query->where('doctor_user_id', $request->user()->id)
                ->orWhere('patient_user_id', $request->user()->id);
        })->where('id', $id)->firstOrFail();

        return response()->json(['data' => $prescription]);
    }

    public function update(Request $request, $id)
    {
        $prescription = AppointmentPrescription::where(function ($query) use ($request) {
            $query->where('doctor_user_id', $request->user()->id)
                ->orWhere('patient_user_id', $request->user()->id);
        })->where('id', $id)->firstOrFail();

        $data = $request->validate([
            'schedule_id' => ['sometimes', 'nullable', 'exists:doctor_schedules,id'],
            'chamber_id' => ['sometimes', 'nullable', 'exists:chambers,id'],
            'appointment_type' => ['sometimes', 'in:HOSPITAL,CHAMBER,ONLINE'],
            'blood_pressure_systolic' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:300'],
            'blood_pressure_diastolic' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:200'],
            'pulse' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:250'],
            'is_smoking' => ['sometimes', 'boolean'],
            'sugar_level' => ['sometimes', 'nullable', 'string', 'max:50'],
            'symptoms' => ['sometimes', 'nullable', 'string'],
            'diagnosis' => ['sometimes', 'nullable', 'string'],
            'medicines' => ['sometimes', 'array'],
            'medicines.*.name' => ['required_with:medicines', 'string', 'max:255'],
            'medicines.*.dose' => ['sometimes', 'nullable', 'string', 'max:255'],
            'medicines.*.schedule' => ['sometimes', 'nullable', 'string', 'max:50'],
            'medicines.*.duration' => ['sometimes', 'nullable', 'string', 'max:100'],
            'medicines.*.notes' => ['sometimes', 'nullable', 'string'],
            'prescription_date' => ['sometimes', 'nullable', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $prescription->update($data);

        return response()->json(['data' => $prescription]);
    }

    public function destroy(Request $request, $id)
    {
        $prescription = AppointmentPrescription::where(function ($query) use ($request) {
            $query->where('doctor_user_id', $request->user()->id)
                ->orWhere('patient_user_id', $request->user()->id);
        })->where('id', $id)->firstOrFail();

        $prescription->delete();

        return response()->json(['message' => 'Prescription deleted']);
    }
}
