<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodDonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'donor_user_id' => $this->donor_user_id,

            'patient_name' => $this->patient_name,
            'patient_gender' => $this->patient_gender,
            'patient_disease' => $this->patient_disease,
            'patient_blood_group' => $this->patient_blood_group,

            'donation_date' => $this->donation_date?->format('Y-m-d'),

            'hospital_name' => $this->hospital_name,
            'hospital_address' => $this->hospital_address,

            'units' => $this->units,
            'notes' => $this->notes,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}