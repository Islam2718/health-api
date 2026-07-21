<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules()
    {
        $userId = $this->route('user') ?? $this->route('id') ?? $this->id;

        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'unique:users,email,' . $userId],
            'phone' => ['sometimes', 'nullable', 'string', 'unique:users,phone,' . $userId],
            'password' => ['sometimes', 'nullable', 'string', 'min:6'],
            'type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:255'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'profile_image' => ['sometimes', 'nullable'],
            'address' => ['sometimes', 'nullable', 'string'],
            'blood_group' => ['sometimes', 'nullable', 'string', 'max:20'],
            'marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}