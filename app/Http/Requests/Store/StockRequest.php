<?php
namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_product_id' => ['required', 'exists:store_products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'transaction_type' => ['required', 'in:purchase,sale,return,adjustment'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'transaction_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_product_id.required' => 'Product selection is required',
            'store_product_id.exists' => 'Selected product does not exist in store',
            'quantity.required' => 'Quantity is required',
            'quantity.min' => 'Quantity must be at least 1',
            'transaction_type.required' => 'Transaction type is required',
            'transaction_type.in' => 'Invalid transaction type',
            'unit_price.required' => 'Unit price is required',
        ];
    }
}