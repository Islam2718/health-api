<?php
namespace App\Http\Controllers\Api;

use App\Application\DTOs\Store\StoreProductDTO;
use App\Application\UseCases\Store\AddProductToStoreUseCase;
use App\Application\UseCases\Store\UpdateStoreProductUseCase;
use App\Application\UseCases\Store\RemoveProductFromStoreUseCase;
use App\Domain\Interfaces\StoreProductRepositoryInterface;
use App\Domain\Interfaces\StoreRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreProductRequest;
use App\Http\Resources\Store\StoreProductResource;
use App\Http\Resources\Store\StoreProductCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StoreProductController extends Controller
{
    public function __construct(
        private StoreProductRepositoryInterface $storeProductRepository,
        private StoreRepositoryInterface $storeRepository,
        private AddProductToStoreUseCase $addProductUseCase,
        // private UpdateStoreProductUseCase $updateProductUseCase,
        // private RemoveProductFromStoreUseCase $removeProductUseCase
    ) {}

    public function index(Request $request, int $storeId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 404);
        }

        $filters = $request->only(['search', 'is_active', 'low_stock', 'per_page']);
        $products = $this->storeProductRepository->getAll($storeId, $filters);
        
        return response()->json([
            'data' => StoreProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage()
            ]
        ]);
    }

    public function store(StoreProductRequest $request, int $storeId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        $dto = new StoreProductDTO(
            id: null,
            store_id: $storeId,
            medicine_id: $request->medicine_id,
            buy_price: $request->buy_price,
            sale_price: $request->sale_price,
            wholesale_price: $request->wholesale_price,
            minimum_stock: $request->minimum_stock ?? 5,
            is_active: true
        );

        $product = $this->addProductUseCase->execute($dto);
        
        return response()->json([
            'message' => 'Product added to store successfully',
            'data' => new StoreProductResource($product)
        ], 201);
    }

    public function show(int $storeId, int $productId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        $product = $this->storeProductRepository->findById($productId);
        if (!$product || $product->store_id !== $storeId) {
            return response()->json([
                'message' => 'Product not found in this store'
            ], 404);
        }

        return response()->json([
            'data' => new StoreProductResource($product)
        ]);
    }

    public function update(StoreProductRequest $request, int $storeId, int $productId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        $product = $this->storeProductRepository->findById($productId);
        if (!$product || $product->store_id !== $storeId) {
            return response()->json([
                'message' => 'Product not found in this store'
            ], 404);
        }

        $dto = new StoreProductDTO(
            id: $productId,
            store_id: $storeId,
            medicine_id: $request->medicine_id,
            buy_price: $request->buy_price,
            sale_price: $request->sale_price,
            wholesale_price: $request->wholesale_price,
            minimum_stock: $request->minimum_stock ?? $product->minimum_stock,
            is_active: $request->is_active ?? $product->is_active
        );

        $updatedProduct = $this->updateProductUseCase->execute($dto);
        
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new StoreProductResource($updatedProduct)
        ]);
    }

    public function destroy(int $storeId, int $productId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        $product = $this->storeProductRepository->findById($productId);
        if (!$product || $product->store_id !== $storeId) {
            return response()->json([
                'message' => 'Product not found in this store'
            ], 404);
        }

        $this->removeProductUseCase->execute($productId);
        
        return response()->json([
            'message' => 'Product removed from store successfully'
        ]);
    }
}