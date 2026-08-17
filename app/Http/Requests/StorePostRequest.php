<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'body' => 'required|string',
            'type' => 'nullable|in:PATIENT_ISSUE,DOCTOR_POST',
            'is_public' => 'nullable|boolean',
            'tags' => 'nullable|string',
        ];
    }
}
