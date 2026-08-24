<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Store;
use App\Infrastructure\Persistence\Models\StoreProduct;
use App\Infrastructure\Persistence\Models\Medicine;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_product_to_store(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/stores/{$store->id}/products", [
                'medicine_id' => $medicine->id,
                'buy_price' => 50.00,
                'sale_price' => 75.00,
                'wholesale_price' => 60.00,
                'minimum_stock' => 10,
            ]);

        $response
            ->assertCreated()
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
        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();

        // Add product first time using Infrastructure Model Factory
        StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        // Try to add same product again
        $response = $this->actingAs($user)
            ->postJson("/api/stores/{$store->id}/products", [
                'medicine_id' => $medicine->id,
                'buy_price' => 55.00,
                'sale_price' => 80.00,
                'wholesale_price' => 65.00,
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['medicine_id']);
    }

    public function test_cannot_add_product_to_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $owner->id]);
        $medicine = Medicine::factory()->create();

        $response = $this->actingAs($otherUser)
            ->postJson("/api/stores/{$store->id}/products", [
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
        $store = Store::factory()->create(['user_id' => $user->id]);
        
        // Create products for the store
        StoreProduct::factory()->count(3)->create([
            'store_id' => $store->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/stores/{$store->id}/products");

        $response
            ->assertOk()
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
        $store = Store::factory()->create(['user_id' => $user->id]);
        
        // Create products with low stock
        StoreProduct::factory()->count(2)->create([
            'store_id' => $store->id,
            'minimum_stock' => 10,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/stores/{$store->id}/products?low_stock=1");

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_view_specific_store_product(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/stores/{$store->id}/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.store_id', $store->id);
    }

    public function test_cannot_view_product_from_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $owner->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        $response = $this->actingAs($otherUser)
            ->getJson("/api/stores/{$store->id}/products/{$product->id}");

        $response->assertForbidden();
    }

    public function test_user_can_update_store_product(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'buy_price' => 50.00,
            'sale_price' => 75.00,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/stores/{$store->id}/products/{$product->id}", [
                'medicine_id' => $product->medicine_id,
                'buy_price' => 55.00,
                'sale_price' => 85.00,
                'wholesale_price' => 65.00,
                'minimum_stock' => 15,
                'is_active' => false,
            ]);

        $response
            ->assertOk()
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
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/stores/{$store->id}/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Product removed from store successfully');

        $this->assertDatabaseMissing('store_products', ['id' => $product->id]);
    }

    public function test_cannot_remove_product_from_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $owner->id]);
        $product = StoreProduct::factory()->create(['store_id' => $store->id]);

        $response = $this->actingAs($otherUser)
            ->deleteJson("/api/stores/{$store->id}/products/{$product->id}");

        $response->assertForbidden();
    }
}