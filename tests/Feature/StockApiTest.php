<?php
namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Store;
use App\Infrastructure\Persistence\Models\StoreProduct;
use App\Infrastructure\Persistence\Models\Stock;
use App\Infrastructure\Persistence\Models\User;
use App\Infrastructure\Persistence\Models\Medicine;
use App\Infrastructure\Persistence\Models\MedicineCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class StockApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        MedicineCompany::create(['name' => 'Test Medicine Company']);
    }

    public function test_authenticated_user_can_add_purchase_stock(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        $response = $this->postJson("/api/stores/{$store->id}/stocks", [
            'store_product_id' => $product->id,
            'quantity' => 100,
            'transaction_type' => 'purchase',
            'unit_price' => 50.00,
            'remarks' => 'Bulk purchase from supplier',
            'transaction_date' => '2024-01-15',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.quantity', 100)
            ->assertJsonPath('data.transaction_type', 'purchase')
            ->assertJsonPath('data.unit_price', '50.00')
            ->assertJsonPath('data.total_price', '5,000.00')
            ->assertJsonPath('data.remarks', 'Bulk purchase from supplier');

        $this->assertDatabaseHas('stocks', [
            'store_product_id' => $product->id,
            'quantity' => 100,
            'transaction_type' => 'purchase',
            'unit_price' => 50.00,
            'total_price' => 5000.00,
            'remarks' => 'Bulk purchase from supplier',
        ]);
    }

    public function test_authenticated_user_can_add_sale_stock(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => 100,
            'transaction_type' => 'purchase',
            'unit_price' => 50.00,
            'total_price' => 5000.00,
        ]);

        $response = $this->postJson("/api/stores/{$store->id}/stocks", [
            'store_product_id' => $product->id,
            'quantity' => 10,
            'transaction_type' => 'sale',
            'unit_price' => 75.00,
            'remarks' => 'Sold to customer',
            'transaction_date' => '2024-01-16',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.quantity', 10)
            ->assertJsonPath('data.transaction_type', 'sale')
            ->assertJsonPath('data.unit_price', '75.00')
            ->assertJsonPath('data.total_price', '750.00');

        $response->assertJsonPath('current_stock', 90);
    }

    public function test_sale_cannot_exceed_current_stock(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => 100,
            'transaction_type' => 'purchase',
        ]);

        $response = $this->postJson("/api/stores/{$store->id}/stocks", [
            'store_product_id' => $product->id,
            'quantity' => 101,
            'transaction_type' => 'sale',
            'unit_price' => 75.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
        $this->assertDatabaseMissing('stocks', [
            'store_product_id' => $product->id,
            'transaction_type' => 'sale',
        ]);
    }

    public function test_cannot_add_stock_to_product_in_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $store = Store::factory()->create(['user_id' => $owner->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        $response = $this->postJson("/api/stores/{$store->id}/stocks", [
            'store_product_id' => $product->id,
            'quantity' => 100,
            'transaction_type' => 'purchase',
            'unit_price' => 50.00,
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_view_stock_records(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        Stock::factory()->count(3)->create([
            'store_product_id' => $product->id,
        ]);

        $response = $this->getJson("/api/stores/{$store->id}/stocks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'store_product_id',
                        'quantity',
                        'transaction_type',
                        'unit_price',
                        'total_price',
                        'remarks',
                        'transaction_date',
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

    public function test_user_can_filter_stocks_by_transaction_type(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'transaction_type' => 'purchase',
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'transaction_type' => 'sale',
        ]);

        $response = $this->getJson("/api/stores/{$store->id}/stocks?transaction_type=purchase");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.transaction_type', 'purchase');
    }

    public function test_user_can_filter_stocks_by_date_range(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'transaction_date' => '2024-01-15',
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'transaction_date' => '2024-01-20',
        ]);

        $response = $this->getJson("/api/stores/{$store->id}/stocks?date_from=2024-01-16&date_to=2024-01-25");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.transaction_date', '2024-01-20T00:00:00.000000Z');
    }

    public function test_user_can_view_product_stock_history(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        Stock::factory()->count(3)->create([
            'store_product_id' => $product->id,
        ]);

        $response = $this->getJson("/api/stores/{$store->id}/products/{$product->id}/stocks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'store_product_id',
                        'quantity',
                        'transaction_type',
                        'unit_price',
                        'total_price',
                        'remarks',
                        'transaction_date',
                    ]
                ],
                'current_stock',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ]
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_current_stock_calculation_is_correct(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => 100,
            'transaction_type' => 'purchase',
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => 30,
            'transaction_type' => 'sale',
        ]);

        Stock::factory()->create([
            'store_product_id' => $product->id,
            'quantity' => 50,
            'transaction_type' => 'purchase',
        ]);

        $response = $this->getJson("/api/stores/{$store->id}/products/{$product->id}/stocks");

        $response->assertStatus(200)
            ->assertJsonPath('current_stock', 120);
    }

    public function test_user_can_view_stock_summary(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine1 = Medicine::factory()->create();
        $medicine2 = Medicine::factory()->create();

        $product1 = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine1->id,
        ]);
        $product2 = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine2->id,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product1->id,
            'quantity' => 100,
            'transaction_type' => 'purchase',
            'unit_price' => 50.00,
            'total_price' => 5000.00,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product1->id,
            'quantity' => 20,
            'transaction_type' => 'sale',
            'unit_price' => 75.00,
            'total_price' => 1500.00,
        ]);

        Stock::factory()->create([
            'store_product_id' => $product2->id,
            'quantity' => 200,
            'transaction_type' => 'purchase',
            'unit_price' => 30.00,
            'total_price' => 6000.00,
        ]);

        $response = $this->getJson("/api/stores/{$store->id}/stocks/summary");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'product_id',
                        'total_purchased',
                        'total_sold',
                        'total_purchase_amount',
                        'total_sale_amount',
                    ]
                ]
            ]);

        $data = $response->json('data');

        $product1Summary = collect($data)->firstWhere('product_id', $product1->id);
        $this->assertEquals(100, $product1Summary['total_purchased']);
        $this->assertEquals(20, $product1Summary['total_sold']);
        $this->assertEquals('5000.00', $product1Summary['total_purchase_amount']);
        $this->assertEquals('1500.00', $product1Summary['total_sale_amount']);

        $product2Summary = collect($data)->firstWhere('product_id', $product2->id);
        $this->assertEquals(200, $product2Summary['total_purchased']);
        $this->assertEquals(0, $product2Summary['total_sold']);
        $this->assertEquals('6000.00', $product2Summary['total_purchase_amount']);
        $this->assertEquals('0.00', $product2Summary['total_sale_amount']);
    }

    public function test_cannot_view_stock_summary_for_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $store = Store::factory()->create(['user_id' => $owner->id]);

        $response = $this->getJson("/api/stores/{$store->id}/stocks/summary");

        $response->assertForbidden();
    }

    public function test_user_can_view_specific_stock_record(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);
        $stock = Stock::factory()->create(['store_product_id' => $product->id]);

        $response = $this->getJson("/api/stores/{$store->id}/stocks/{$stock->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $stock->id)
            ->assertJsonPath('data.quantity', $stock->quantity)
            ->assertJsonPath('data.transaction_type', $stock->transaction_type);
    }

    public function test_cannot_view_stock_record_from_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $store = Store::factory()->create(['user_id' => $owner->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);
        $stock = Stock::factory()->create(['store_product_id' => $product->id]);

        $response = $this->getJson("/api/stores/{$store->id}/stocks/{$stock->id}");

        $response->assertForbidden();
    }

    public function test_cannot_add_stock_with_negative_quantity(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        $response = $this->postJson("/api/stores/{$store->id}/stocks", [
            'store_product_id' => $product->id,
            'quantity' => -10,
            'transaction_type' => 'purchase',
            'unit_price' => 50.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_cannot_add_stock_with_negative_price(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        $response = $this->postJson("/api/stores/{$store->id}/stocks", [
            'store_product_id' => $product->id,
            'quantity' => 10,
            'transaction_type' => 'purchase',
            'unit_price' => -50.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['unit_price']);
    }

    public function test_invalid_transaction_type_is_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $medicine = Medicine::factory()->create();
        $product = StoreProduct::factory()->create([
            'store_id' => $store->id,
            'medicine_id' => $medicine->id,
        ]);

        $response = $this->postJson("/api/stores/{$store->id}/stocks", [
            'store_product_id' => $product->id,
            'quantity' => 10,
            'transaction_type' => 'invalid_type',
            'unit_price' => 50.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['transaction_type']);
    }
}
