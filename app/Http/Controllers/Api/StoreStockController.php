<?php
namespace App\Http\Controllers\Api;

use App\Application\DTOs\Store\StockDTO;
use App\Application\UseCases\Store\AddStockUseCase;
use App\Domain\Interfaces\StockRepositoryInterface;
use App\Domain\Interfaces\StoreProductRepositoryInterface;
use App\Domain\Interfaces\StoreRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StockRequest;
use App\Http\Resources\StockResource;
use App\Http\Resources\StockCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StoreStockController extends Controller
{
    public function __construct(
        private StockRepositoryInterface $stockRepository,
        private StoreProductRepositoryInterface $storeProductRepository,
        private StoreRepositoryInterface $storeRepository,
        private AddStockUseCase $addStockUseCase
    ) {}

    public function index(Request $request, int $storeId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        $filters = $request->only(['transaction_type', 'date_from', 'date_to', 'per_page']);
        $stocks = $this->stockRepository->getAll($storeId, $filters);
        
        return response()->json([
            'data' => StockResource::collection($stocks),
            'meta' => [
                'current_page' => $stocks->currentPage(),
                'per_page' => $stocks->perPage(),
                'total' => $stocks->total(),
                'last_page' => $stocks->lastPage()
            ]
        ]);
    }

    public function store(StockRequest $request, int $storeId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        // Check if product belongs to store
        $storeProduct = $this->storeProductRepository->findById($request->store_product_id);
        if (!$storeProduct || $storeProduct->store_id !== $storeId) {
            return response()->json([
                'message' => 'Product not found in this store'
            ], 404);
        }

        $dto = new StockDTO(
            id: null,
            store_product_id: $request->store_product_id,
            quantity: $request->quantity,
            transaction_type: $request->transaction_type,
            unit_price: $request->unit_price,
            total_price: 0, // Will be calculated in use case
            remarks: $request->remarks,
            transaction_date: $request->transaction_date ?? now()->toDateString()
        );

        $stock = $this->addStockUseCase->execute($dto);
        $currentStock = $this->stockRepository->getCurrentStock($request->store_product_id);
        
        return response()->json([
            'message' => 'Stock added successfully',
            'data' => new StockResource($stock),
            'current_stock' => $currentStock,
        ], 201);
    }

    public function show(int $storeId, int $stockId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        $stock = $this->stockRepository->findById($stockId);
        if (!$stock) {
            return response()->json([
                'message' => 'Stock record not found'
            ], 404);
        }

        // Check if stock belongs to a product in this store
        $storeProduct = $this->storeProductRepository->findById($stock->store_product_id);
        if (!$storeProduct || $storeProduct->store_id !== $storeId) {
            return response()->json([
                'message' => 'Stock record not found in this store'
            ], 404);
        }

        return response()->json([
            'data' => new StockResource($stock)
        ]);
    }

    public function getProductStock(Request $request, int $storeId, int $productId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        // Check if product belongs to store
        $storeProduct = $this->storeProductRepository->findById($productId);
        if (!$storeProduct || $storeProduct->store_id !== $storeId) {
            return response()->json([
                'message' => 'Product not found in this store'
            ], 404);
        }

        $filters = $request->only(['transaction_type', 'date_from', 'date_to', 'per_page']);
        $stocks = $this->stockRepository->getAll($productId, $filters);
        
        $currentStock = $this->stockRepository->getCurrentStock($productId);
        
        return response()->json([
            'data' => StockResource::collection($stocks),
            'current_stock' => $currentStock,
            'meta' => [
                'current_page' => $stocks->currentPage(),
                'per_page' => $stocks->perPage(),
                'total' => $stocks->total(),
                'last_page' => $stocks->lastPage()
            ]
        ]);
    }

    public function getStockSummary(int $storeId): JsonResponse
    {
        // Check if store belongs to user
        $store = $this->storeRepository->findById($storeId);
        if (!$store || $store->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 403);
        }

        $summary = $this->stockRepository->getStockSummary($storeId);
        
        return response()->json([
            'data' => $summary
        ]);
    }
}