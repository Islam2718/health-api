<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodDonorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'address' => $this->address,
            'donor_interest' => (bool) $this->donor_interest,
        ];
    }
}