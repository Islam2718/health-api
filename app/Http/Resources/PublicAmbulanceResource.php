<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicAmbulanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'brand_model' =>
                $this->brand_model,

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

            'owner' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'phone' => $this->user->phone,
                    'address' => $this->user->address,
                    'profile_image' => $this->user->profile_image,
                ];
            }),
        ];
    }
}