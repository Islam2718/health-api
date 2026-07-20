<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'sometimes|string',
            'email' => 'nullable|email|unique:users,email,' . $this->id,
            'phone' => 'nullable|unique:users,phone,' . $this->id,
            'username' => 'nullable|unique:users,username,' . $this->id,
            'password' => 'nullable|min:6',
        ];
    }
}