<?php
namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\Store;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class StoreApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_authenticated_user_can_create_store(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/stores', [
            'store_name' => 'Health Pharmacy',
            'store_address' => '123 Main Street, Dhaka',
            'trade_license_no' => 'TL-2024-001',
            'phone' => '+8801234567890',
            'email' => 'health@pharmacy.com',
            'description' => 'Our first pharmacy store',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.store_name', 'Health Pharmacy')
            ->assertJsonPath('data.store_address', '123 Main Street, Dhaka')
            ->assertJsonPath('data.trade_license_no', 'TL-2024-001');

        $this->assertDatabaseHas('stores', [
            'user_id' => $user->id,
            'store_name' => 'Health Pharmacy',
            'trade_license_no' => 'TL-2024-001',
        ]);
    }

    public function test_store_creation_fails_with_duplicate_trade_license(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Store::factory()->create([
            'user_id' => $user->id,
            'trade_license_no' => 'TL-2024-001',
        ]);

        $response = $this->postJson('/api/stores', [
            'store_name' => 'Another Pharmacy',
            'store_address' => '456 Another Street, Dhaka',
            'trade_license_no' => 'TL-2024-001',
            'phone' => '+8801234567891',
            'email' => 'another@pharmacy.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['trade_license_no']);
    }

    public function test_user_can_view_own_stores(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($user);

        Store::factory()->count(3)->create(['user_id' => $user->id]);
        Store::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->getJson('/api/stores');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'store_name',
                        'store_address',
                        'trade_license_no',
                        'phone',
                        'email',
                        'description',
                        'is_active',
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

    public function test_user_can_search_stores_by_name(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Store::factory()->create([
            'user_id' => $user->id,
            'store_name' => 'Health Pharmacy',
        ]);

        Store::factory()->create([
            'user_id' => $user->id,
            'store_name' => 'Wellness Store',
        ]);

        $response = $this->getJson('/api/stores?search=Health');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.store_name', 'Health Pharmacy');
    }

    public function test_user_can_view_specific_store(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $store = Store::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/stores/{$store->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $store->id)
            ->assertJsonPath('data.store_name', $store->store_name);
    }

    public function test_user_cannot_view_other_users_store(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($user);
        
        $store = Store::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->getJson("/api/stores/{$store->id}");

        $response->assertNotFound();
    }

    public function test_user_can_update_own_store(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $store = Store::factory()->create([
            'user_id' => $user->id,
            'store_name' => 'Old Name',
        ]);

        $response = $this->putJson("/api/stores/{$store->id}", [
            'store_name' => 'Updated Pharmacy',
            'store_address' => 'Updated Address, Dhaka',
            'trade_license_no' => 'TL-2024-002',
            'phone' => '+8801234567899',
            'email' => 'updated@pharmacy.com',
            'is_active' => false,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.store_name', 'Updated Pharmacy')
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'store_name' => 'Updated Pharmacy',
            'trade_license_no' => 'TL-2024-002',
        ]);
    }

    public function test_user_can_only_update_own_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);
        
        $store = Store::factory()->create(['user_id' => $owner->id]);

        $response = $this->putJson("/api/stores/{$store->id}", [
            'store_name' => 'Hacked Store',
            'store_address' => 'Hacked Address',
            'trade_license_no' => 'TL-HACKED',
        ]);

        $response->assertNotFound();
    }

    public function test_user_can_delete_own_store(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $store = Store::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/stores/{$store->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Store deleted successfully');

        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    }

    public function test_user_cannot_delete_other_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);
        
        $store = Store::factory()->create(['user_id' => $owner->id]);

        $response = $this->deleteJson("/api/stores/{$store->id}");

        $response->assertNotFound();
    }

    public function test_unauthenticated_user_cannot_access_stores(): void
    {
        $response = $this->getJson('/api/stores');
        $response->assertUnauthorized();

        $response = $this->postJson('/api/stores', [
            'store_name' => 'Test Store',
            'store_address' => 'Test Address',
            'trade_license_no' => 'TL-001',
        ]);
        $response->assertUnauthorized();
    }
}