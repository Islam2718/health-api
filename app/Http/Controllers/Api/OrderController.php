<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Infrastructure\Persistence\Models\Order;
use App\Infrastructure\Persistence\Models\Store;
use App\Infrastructure\Persistence\Models\StoreProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, int $storeId): JsonResponse
    {
        $store = Store::query()->where('id', $storeId)->where('is_active', true)->first();

        if (! $store) {
            return response()->json(['message' => 'Store not found or inactive'], 404);
        }

        $data = $request->validated();
        $order = DB::transaction(function () use ($data, $storeId): Order {
            $items = collect($data['items'])->map(function (array $item) use ($storeId): array {
                $product = StoreProduct::query()
                    ->with('medicine')
                    ->where('id', $item['store_product_id'])
                    ->where('store_id', $storeId)
                    ->where('is_active', true)
                    ->first();

                if (! $product || $product->sale_price === null) {
                    abort(422, 'One or more products are unavailable for this store');
                }

                if ($product->current_stock < $item['quantity']) {
                    abort(422, "Insufficient stock for {$product->medicine->name}");
                }

                $unitPrice = (float) $product->sale_price;

                return [
                    'store_product_id' => $product->id,
                    'medicine_id' => $product->medicine_id,
                    'medicine_name' => $product->medicine->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $item['quantity'],
                ];
            });

            $subtotal = $items->sum('total_price');
            $discount = min((float) ($data['discount'] ?? 0), $subtotal);
            $deliveryFee = (float) ($data['delivery_fee'] ?? 0);

            $order = Order::query()->create([
                'user_id' => $data['customer_id'] ?? null,
                'store_id' => $storeId, 
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'payment_method' => strtoupper($data['payment_method'] ?? 'CASH'),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => $subtotal - $discount + $deliveryFee,
                'shipping_address' => $data['shipping_address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'placed_at' => now(),
            ]);

            $order->items()->createMany($items->all());

            return $order;
        });

        return response()->json([
            'message' => 'Order created successfully',
            'data' => new OrderResource($order->load(['customer', 'store', 'items'])),
        ], 201);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['store', 'items'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function showMine(Request $request, int $orderId): JsonResponse
    {
        $order = Order::query()
            ->with(['customer', 'store', 'items'])
            ->where('user_id', $request->user()->id)
            ->find($orderId);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(['data' => new OrderResource($order)]);
    }

    public function index(Request $request, int $storeId): JsonResponse
    {
        if (! $this->ownedStore($request, $storeId)) {
            return response()->json(['message' => 'Store not found or unauthorized'], 404);
        }

        $orders = Order::query()
            ->with(['customer', 'items'])
            ->where('store_id', $storeId)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, int $storeId, int $orderId): JsonResponse
    {
        if (! $this->ownedStore($request, $storeId)) {
            return response()->json(['message' => 'Store not found or unauthorized'], 404);
        }

        $order = Order::query()
            ->with(['customer', 'store', 'items'])
            ->where('store_id', $storeId)
            ->find($orderId);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(['data' => new OrderResource($order)]);
    }

    public function updateStatus(Request $request, int $storeId, int $orderId): JsonResponse
    {
        if (! $this->ownedStore($request, $storeId)) {
            return response()->json(['message' => 'Store not found or unauthorized'], 404);
        }

        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
        ]);
        $order = Order::query()->where('store_id', $storeId)->find($orderId);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update(['status' => $data['status']]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => new OrderResource($order->load(['customer', 'store', 'items'])),
        ]);
    }

    private function ownedStore(Request $request, int $storeId): bool
    {
        return Store::query()->where('id', $storeId)->where('user_id', $request->user()->id)->exists();
    }
}
