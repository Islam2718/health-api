<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmbulanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'brand_model' => $this->brand_model,

            'license_plate_number' =>
                $this->license_plate_number,

            'phone_number' =>
                $this->phone_number,

            'ambulance_type' =>
                $this->ambulance_type,

            'equipment_list' =>
                $this->equipment_list ?? [],

            'description' =>
                $this->description,

            'address' =>
                $this->address,

            'is_active' =>
                $this->is_active,

            'owner' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'phone' => $this->user->phone,
                ];
            }),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}