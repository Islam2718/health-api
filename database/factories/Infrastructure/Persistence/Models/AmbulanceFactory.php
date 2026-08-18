<?php

namespace Database\Factories\Infrastructure\Persistence\Models;

use App\Infrastructure\Persistence\Models\User;
use App\Infrastructure\Persistence\Models\Ambulance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AmbulanceFactory extends Factory
{
    protected $model = Ambulance::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'brand_model' => fake()->randomElement([
                'Toyota Hiace',
                'Nissan Caravan',
                'Mitsubishi Delica',
                'Toyota Noah',
            ]),

            'license_plate_number' => strtoupper(
                fake()->bothify('DHAKA-??-####')
            ),

            'phone_number' => fake()->numerify(
                '017########'
            ),

            'ambulance_type' => fake()->randomElement([
                'AC',
                'NonAC',
                'AIR',
                'Freeze',
            ]),

            'equipment_list' => [
                'Oxygen',
            ],

            'description' => fake()->sentence(),

            'address' => fake()->address(),

            'is_active' => true,
        ];
    }
}
