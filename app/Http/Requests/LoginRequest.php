<?php 
// app/Http/Requests/LoginRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        if ($this->filled('access_token')) {
            return [
                'access_token' => ['required', 'string'],
            ];
        }

        return [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string']
        ];
    }
}