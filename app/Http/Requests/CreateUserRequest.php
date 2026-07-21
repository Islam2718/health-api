<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'type' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'profile_image' => ['nullable'],
            'address' => ['nullable', 'string'],
            'blood_group' => ['nullable', 'string', 'max:20'],
            'marital_status' => ['nullable', 'string', 'max:50'],
        ];
    }
}
