<?php
namespace App\Http\Controllers\Api;

use App\Application\DTOs\Store\StoreDTO;
use App\Application\UseCases\Store\CreateStoreUseCase;
use App\Application\UseCases\Store\UpdateStoreUseCase;
use App\Application\UseCases\Store\DeleteStoreUseCase;
use App\Domain\Interfaces\StoreRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreRequest;
use App\Http\Resources\StoreResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
// auth
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository,
        private CreateStoreUseCase $createStoreUseCase,
        private UpdateStoreUseCase $updateStoreUseCase,
        private DeleteStoreUseCase $deleteStoreUseCase
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'per_page']);
        $stores = $this->storeRepository->getAll(Auth::id(), $filters);

        return response()->json([
            'data' => StoreResource::collection($stores),
            'meta' => [
                'current_page' => $stores->currentPage(),
                'per_page' => $stores->perPage(),
                'total' => $stores->total(),
                'last_page' => $stores->lastPage()
            ]
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $dto = new StoreDTO(
            id: null,
            user_id: Auth::id(),
            store_name: $request->store_name,
            store_address: $request->store_address,
            trade_license_no: $request->trade_license_no,
            phone: $request->phone,
            email: $request->email,
            description: $request->description,
            is_active: true
        );

        $store = $this->createStoreUseCase->execute($dto);

        return response()->json([
            'message' => 'Store created successfully',
            'data' => new StoreResource($store)
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $store = $this->storeRepository->findById($id);

        if (!$store || $store->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'data' => new StoreResource($store)
        ]);
    }

    public function update(StoreRequest $request, int $id): JsonResponse
    {
        $store = $this->storeRepository->findById($id);

        if (!$store || $store->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 404);
        }

        $dto = new StoreDTO(
            id: $id,
            user_id: Auth::id(),
            store_name: $request->store_name,
            store_address: $request->store_address,
            trade_license_no: $request->trade_license_no,
            phone: $request->phone,
            email: $request->email,
            description: $request->description,
            is_active: $request->is_active ?? true
        );

        $updatedStore = $this->updateStoreUseCase->execute($dto);

        return response()->json([
            'message' => 'Store updated successfully',
            'data' => new StoreResource($updatedStore)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $store = $this->storeRepository->findById($id);

        if (!$store || $store->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Store not found or unauthorized'
            ], 404);
        }

        $this->deleteStoreUseCase->execute($id);

        return response()->json([
            'message' => 'Store deleted successfully'
        ]);
    }
}
