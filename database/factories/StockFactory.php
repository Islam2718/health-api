<?php
// database/factories/StockFactory.php
namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Stock;
use App\Infrastructure\Persistence\Models\StoreProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    protected $model = Stock::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(10, 200);
        $unitPrice = $this->faker->randomFloat(2, 10, 100);
        // শুধু purchase এবং sale ব্যবহার করছি কারণ টেস্টে এগুলো ইউজ করা হয়েছে
        $transactionTypes = ['purchase', 'sale'];
        
        return [
            'store_product_id' => StoreProduct::factory(),
            'quantity' => $quantity,
            'transaction_type' => $this->faker->randomElement($transactionTypes),
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'remarks' => $this->faker->sentence(),
            'transaction_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}