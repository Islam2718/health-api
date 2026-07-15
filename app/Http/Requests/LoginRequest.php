<?php 
// app/Http/Requests/LoginRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string']
        ];
    }
}