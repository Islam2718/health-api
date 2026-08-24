<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'medicine_id' => $this->medicine_id,
            'medicine_name' => $this->medicine->name ?? null,
            'medicine_generic_name' => $this->medicine->generic_name ?? null,
            'buy_price' => number_format($this->buy_price, 2),
            'sale_price' => number_format($this->sale_price, 2),
            'wholesale_price' => number_format($this->wholesale_price, 2),
            'minimum_stock' => $this->minimum_stock,
            'is_active' => $this->is_active,
            'current_stock' => $this->current_stock ?? 0,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}