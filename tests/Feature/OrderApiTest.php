<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Medicine;
use App\Infrastructure\Persistence\Models\MedicineCompany;
use App\Infrastructure\Persistence\Models\Stock;
use App\Infrastructure\Persistence\Models\Store;
use App\Infrastructure\Persistence\Models\StoreProduct;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_use_an_existing_customer_id(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create(['phone' => '01700000000']);
        $store = Store::factory()->create(['user_id' => $owner->id]);
        $product = $this->createProduct($store, 100);
        Sanctum::actingAs($owner);

        $response = $this->postJson("/api/stores/{$store->id}/orders", [
            'customer_id' => $customer->id,
            'items' => [
                ['store_product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.customer_id', $customer->id)
            ->assertJsonPath('data.items.0.medicine_name', $product->medicine->name)
            ->assertJsonPath('data.total', '20.00')
            ->assertJsonPath('data.payment_method', 'CASH');

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('orders', ['user_id' => $customer->id, 'store_id' => $store->id]);
    }

    public function test_order_can_be_created_without_a_customer(): void
    {
        $owner = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $owner->id]);
        $product = $this->createProduct($store, 10);
        Sanctum::actingAs($owner);

        $response = $this->postJson("/api/stores/{$store->id}/orders", [
            'items' => [
                ['store_product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.customer_id', null)
            ->assertJsonMissingPath('data.customer.name')
            ->assertJsonMissingPath('data.customer.phone')
            ->assertJsonPath('data.payment_method', 'CASH');

        $this->assertDatabaseHas('orders', [
            'user_id' => null,
            'store_id' => $store->id,
            'payment_method' => 'CASH',
        ]);
    }

    private function createProduct(Store $store, int $stockQuantity): StoreProduct
    {
        $company = MedicineCompany::query()->create(['name' => 'Test Company']);
        $medicine = Medicine::query()->create([
            'name' => 'Test Medicine',
            'company_id' => $company->id,
        ]);
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
            'sale_price' => 10,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => $stockQuantity,
            'transaction_type' => 'purchase',
            'unit_price' => 5,
            'total_price' => $stockQuantity * 5,
        ]);

        return $product;
    }
}
