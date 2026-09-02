<?php
namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Store;
use App\Infrastructure\Persistence\Models\StoreProduct;
use App\Infrastructure\Persistence\Models\Stock;
use App\Infrastructure\Persistence\Models\Medicine;
use App\Infrastructure\Persistence\Models\MedicineCompany;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class StoreProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        MedicineCompany::create(['name' => 'Test Medicine Company']);
    }

    public function test_authenticated_user_can_add_product_to_store(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();

        $response = $this->postJson("/api/stores/{$store->id}/products", [
            'medicine_id' => $medicine->id,
            'buy_price' => 50.00,
            'sale_price' => 75.00,
            'wholesale_price' => 60.00,
            'minimum_stock' => 10,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.medicine_id', $medicine->id)
            ->assertJsonPath('data.buy_price', '50.00')
            ->assertJsonPath('data.sale_price', '75.00')
            ->assertJsonPath('data.wholesale_price', '60.00')
            ->assertJsonPath('data.minimum_stock', 10);

        $this->assertDatabaseHas('store_products', [
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
            'buy_price' => 50.00,
            'sale_price' => 75.00,
            'wholesale_price' => 60.00,
        ]);
    }

    public function test_cannot_add_duplicate_product_to_store(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();

        StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        $response = $this->postJson("/api/stores/{$store->id}/products", [
            'medicine_id' => $medicine->id,
            'buy_price' => 55.00,
            'sale_price' => 80.00,
            'wholesale_price' => 65.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['medicine_id']);
    }

    public function test_cannot_add_product_to_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $store = Store::factory()->create(['user_id' => $owner->id]);
        $medicine = Medicine::factory()->create();

        $response = $this->postJson("/api/stores/{$store->id}/products", [
            'medicine_id' => $medicine->id,
            'buy_price' => 50.00,
            'sale_price' => 75.00,
            'wholesale_price' => 60.00,
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_view_store_products(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);

        StoreProduct::factory()->count(3)->create([
            'store_id' => $store->id,
        ]);

        $response = $this->getJson("/api/stores/{$store->id}/products");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'store_id',
                        'medicine_id',
                        'medicine_name',
                        'buy_price',
                        'sale_price',
                        'wholesale_price',
                        'minimum_stock',
                        'is_active',
                        'current_stock',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ]
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_user_can_filter_low_stock_products(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);

        StoreProduct::factory()->count(2)->create([
            'store_id' => $store->id,
            'minimum_stock' => 10,
        ]);

        $response = $this->getJson("/api/stores/{$store->id}/products?low_stock=1");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_view_specific_store_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        $response = $this->getJson("/api/stores/{$store->id}/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.store_id', $store->id);
    }

    public function test_product_stock_excludes_sold_quantity_in_list_and_details(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => 100,
            'transaction_type' => 'purchase',
        ]);
        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => 5,
            'transaction_type' => 'sale',
        ]);
        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => 10,
            'transaction_type' => 'sale',
        ]);

        $this->getJson("/api/stores/{$store->id}/products")
            ->assertOk()
            ->assertJsonPath('data.0.current_stock', 85);

        $this->getJson("/api/stores/{$store->id}/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.current_stock', 85);
    }

    public function test_cannot_view_product_from_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $store = Store::factory()->create(['user_id' => $owner->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        $response = $this->getJson("/api/stores/{$store->id}/products/{$product->id}");

        $response->assertForbidden();
    }

    public function test_user_can_update_store_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'buy_price' => 50.00,
            'sale_price' => 75.00,
        ]);

        $response = $this->putJson("/api/stores/{$store->id}/products/{$product->id}", [
            'medicine_id' => $product->medicine_id,
            'buy_price' => 55.00,
            'sale_price' => 85.00,
            'wholesale_price' => 65.00,
            'minimum_stock' => 15,
            'is_active' => false,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.buy_price', '55.00')
            ->assertJsonPath('data.sale_price', '85.00')
            ->assertJsonPath('data.wholesale_price', '65.00')
            ->assertJsonPath('data.minimum_stock', 15)
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('store_products', [
            'id' => $product->id,
            'buy_price' => 55.00,
            'sale_price' => 85.00,
        ]);
    }

    public function test_user_can_remove_product_from_store(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        $response = $this->deleteJson("/api/stores/{$store->id}/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Product removed from store successfully');

        $this->assertDatabaseMissing('store_products', ['id' => $product->id]);
    }

    public function test_cannot_remove_product_from_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $store = Store::factory()->create(['user_id' => $owner->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        $response = $this->deleteJson("/api/stores/{$store->id}/products/{$product->id}");

        $response->assertForbidden();
    }
}
