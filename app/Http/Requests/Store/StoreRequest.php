<?php
namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'store_name' => ['required', 'string', 'max:255'],
            'store_address' => ['required', 'string'],
            'trade_license_no' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string'],
        ];

        // For update, make trade_license_no unique except current record
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['trade_license_no'] = [
                'required', 
                'string', 
                'max:50',
                'unique:stores,trade_license_no,' . $this->route('id')
            ];
            $rules['is_active'] = ['boolean'];
        } else {
            $rules['trade_license_no'][] = 'unique:stores,trade_license_no';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'store_name.required' => 'Store name is required',
            'store_address.required' => 'Store address is required',
            'trade_license_no.required' => 'Trade license number is required',
            'trade_license_no.unique' => 'This trade license number is already registered',
        ];
    }
}