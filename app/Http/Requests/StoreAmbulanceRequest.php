<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAmbulanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'brand_model' => [
                'nullable',
                'string',
                'max:255',
            ],

            'license_plate_number' => [
                'required',
                'string',
                'max:255',
                'unique:ambulances,license_plate_number',
            ],

            'phone_number' => [
                'required',
                'string',
                'max:30',
            ],

            'ambulance_type' => [
                'nullable',
                Rule::in([
                    'AC',
                    'NonAC',
                    'AIR',
                    'Freeze',
                ]),
            ],

            'equipment_list' => [
                'nullable',
                'array',
            ],

            'equipment_list.*' => [
                'string',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'address' => [
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