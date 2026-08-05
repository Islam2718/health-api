<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FindOrCreateUserByPhoneRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'unique:users,email'],
            'password' => ['sometimes', 'nullable', 'string', 'min:6'],
            'type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:255'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'address' => ['sometimes', 'nullable', 'string'],
            'blood_group' => ['sometimes', 'nullable', 'string', 'max:20'],
            'marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}
