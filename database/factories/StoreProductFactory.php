<?php
// database/factories/StoreProductFactory.php
namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Store;
use App\Infrastructure\Persistence\Models\StoreProduct;
use App\Infrastructure\Persistence\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreProductFactory extends Factory
{
    protected $model = StoreProduct::class;

    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'medicine_id' => Medicine::factory(),
            'buy_price' => $this->faker->randomFloat(2, 10, 100),
            'sale_price' => $this->faker->randomFloat(2, 20, 150),
            'wholesale_price' => $this->faker->randomFloat(2, 15, 120),
            'minimum_stock' => $this->faker->numberBetween(5, 20),
            'is_active' => true,
        ];
    }
}