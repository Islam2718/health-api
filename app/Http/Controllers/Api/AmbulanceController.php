<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\CreateAmbulance;
use App\Application\UseCases\DeleteAmbulance;
use App\Application\UseCases\GetMyAmbulances;
use App\Application\UseCases\GetPublicAmbulances;
use App\Application\UseCases\UpdateAmbulance;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmbulanceRequest;
use App\Http\Requests\UpdateAmbulanceRequest;
use App\Http\Resources\AmbulanceResource;
use App\Http\Resources\PublicAmbulanceResource;
use App\Infrastructure\Persistence\Models\Ambulance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AmbulanceController extends Controller
{
    /**
     * ambulances.index
     */
    public function index(
        Request $request,
        GetMyAmbulances $useCase
    ) {
        $ambulances = $useCase->execute(
            Auth::id(),
            (int) $request->input('per_page', 15)
        );

        return AmbulanceResource::collection($ambulances);
    }

    /**
     * ambulances.store
     */
    public function store(
        StoreAmbulanceRequest $request,
        CreateAmbulance $useCase
    ): JsonResponse {
        $ambulance = $useCase->execute(
            Auth::id(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Ambulance created successfully.',
            'data' => new AmbulanceResource(
                $ambulance->load('user')
            ),
        ], 201);
    }

    /**
     * ambulances.show
     */
    public function show(
        int $id
    ): JsonResponse {
        $ambulance = Ambulance::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->with('user')
            ->firstOrFail();

        return response()->json([
            'data' => new AmbulanceResource($ambulance),
        ]);
    }

    /**
     * ambulances.update(:id)
     */
    public function update(
        UpdateAmbulanceRequest $request,
        int $id,
        UpdateAmbulance $useCase
    ): JsonResponse {
        $ambulance = $useCase->execute(
            $id,
            Auth::id(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Ambulance updated successfully.',
            'data' => new AmbulanceResource(
                $ambulance->load('user')
            ),
        ]);
    }

    /**
     * ambulances.destroy(:id)
     */
    public function destroy(
        int $id,
        DeleteAmbulance $useCase
    ): JsonResponse {
        $useCase->execute(
            $id,
            Auth::id()
        );

        return response()->json([
            'message' => 'Ambulance deleted successfully.',
        ]);
    }

    /**
     * ambulances.publicIndex
     */
    public function publicIndex(
        Request $request,
        GetPublicAmbulances $useCase
    ) {
        $filters = $request->only([
            'ambulance_type',
            'equipment',
            'address',
            'search',
            'random',
            'per_page',
        ]);

        $ambulances = $useCase->execute($filters);

        return PublicAmbulanceResource::collection(
            $ambulances
        );
    }

    /**
     * ambulances.publicShow(:id)
     */
    public function publicShow(
        int $id
    ): JsonResponse {
        $ambulance = Ambulance::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->with('user')
            ->firstOrFail();

        return response()->json([
            'data' => new PublicAmbulanceResource(
                $ambulance
            ),
        ]);
    }
}