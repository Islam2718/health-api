<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Medicine;
use App\Infrastructure\Persistence\Models\MedicineCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineFactory extends Factory
{
    protected $model = Medicine::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'generic_name' => fake()->word(),
            'weight' => fake()->randomFloat(2, 0.1, 100),
            'suggestion_price' => fake()->randomFloat(2, 10, 500),
            'type' => fake()->word(),
            'description' => fake()->sentence(),
            'company_id' => 1,
            'medicine_category_id' => MedicineCategory::factory(),
        ];
    }
}