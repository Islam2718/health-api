<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAmbulanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $ambulanceId = $this->route('id');

        return [
            'brand_model' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'license_plate_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'ambulances',
                    'license_plate_number'
                )->ignore($ambulanceId),
            ],

            'phone_number' => [
                'sometimes',
                'required',
                'string',
                'max:30',
            ],

            'ambulance_type' => [
                'sometimes',
                'nullable',
                Rule::in([
                    'AC',
                    'NonAC',
                    'AIR',
                    'Freeze',
                ]),
            ],

            'equipment_list' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'equipment_list.*' => [
                'string',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:2000',
            ],

            'address' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}