<?php
namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Store;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'store_name' => $this->faker->company() . ' Pharmacy',
            'store_address' => $this->faker->address(),
            'trade_license_no' => 'TL-' . $this->faker->unique()->numberBetween(1000, 9999),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}