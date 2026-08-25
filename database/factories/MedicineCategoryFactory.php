<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\MedicineCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineCategoryFactory extends Factory
{
    protected $model = MedicineCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Antibiotic',
                'Antidepressant',
                'Analgesic',
                'Antihistamine',
                'Antacid',
                'Antiviral',
            ]),
            'is_active' => true,
        ];
    }
}