<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'customer' => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name,
                'phone' => $this->customer?->phone,
            ],
            'store' => [
                'id' => $this->store?->id,
                'name' => $this->store?->store_name,
            ],
            'items' => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'store_product_id' => $item->store_product_id,
                'medicine_id' => $item->medicine_id,
                'medicine_name' => $item->medicine_name,
                'quantity' => $item->quantity,
                'unit_price' => number_format($item->unit_price, 2),
                'total_price' => number_format($item->total_price, 2),
            ]),
            'subtotal' => number_format($this->subtotal, 2),
            'discount' => number_format($this->discount, 2),
            'delivery_fee' => number_format($this->delivery_fee, 2),
            'total' => number_format($this->total, 2),
            'shipping_address' => $this->shipping_address,
            'contact_phone' => $this->contact_phone,
            'notes' => $this->notes,
            'placed_at' => $this->placed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
