<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBloodDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'patient_name' => [
                'required',
                'string',
                'max:255',
            ],

            'patient_gender' => [
                'nullable',
                'string',
                Rule::in([
                    'Male',
                    'Female',
                    'Other',
                ]),
            ],

            'patient_disease' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'patient_blood_group' => [
                'nullable',
                'string',
                'max:10',
            ],

            'donation_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'hospital_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'hospital_address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'units' => [
                'required',
                'integer',
                'min:1',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }
}