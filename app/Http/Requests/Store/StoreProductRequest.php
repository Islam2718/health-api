<?php
namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medicine_id' => ['required', 'exists:medicines,id'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'wholesale_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'medicine_id.required' => 'Medicine selection is required',
            'medicine_id.exists' => 'Selected medicine does not exist',
            'buy_price.required' => 'Buy price is required',
            'sale_price.required' => 'Sale price is required',
            'wholesale_price.required' => 'Wholesale price is required',
        ];
    }
}