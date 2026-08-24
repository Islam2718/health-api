<?php
namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineFactory extends Factory
{
    protected $model = Medicine::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->word(),
            'generic_name' => $this->faker->word(),
            'category' => $this->faker->randomElement(['Antibiotic', 'Painkiller', 'Antihistamine', 'Antidepressant']),
            'manufacturer' => $this->faker->company(),
            'strength' => $this->faker->randomElement(['500mg', '250mg', '100mg', '50mg', '10mg']),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'is_active' => true,
        ];
    }
}