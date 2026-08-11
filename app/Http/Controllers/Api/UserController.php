<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Application\UseCases\User\{
    CreateUserUseCase,
    DeleteUserUseCase,
    FindOrCreateUserByPhoneUseCase,
    FindUserByPhoneUseCase,
    GetAllUsersUseCase,
    GetUserUseCase,
    UpdateUserUseCase
};
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\FindOrCreateUserByPhoneRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Infrastructure\Persistence\Models\User;

class UserController extends Controller
{
    public function index(GetAllUsersUseCase $useCase)
    {
        return response()->json($useCase->execute());
    }

    public function store(CreateUserRequest $request, CreateUserUseCase $useCase)
    {
        return response()->json(
            $useCase->execute($request->validated()),
            201
        );
    }

    public function show($id, GetUserUseCase $useCase)
    {
        return response()->json($useCase->execute($id));
    }

    public function findByPhone($phone, FindUserByPhoneUseCase $useCase)
    {
        $user = $useCase->execute($phone);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $patientAppointments = $user->appointmentsAsPatient()
            ->with(['doctor', 'hospital', 'chamber'])
            ->get();

        $doctorAppointments = $user->appointmentsAsDoctor()
            ->with(['patient', 'hospital', 'chamber'])
            ->get();

        $appointments = $patientAppointments->merge($doctorAppointments)->unique('id')->values();

        return response()->json([
            'data' => $user,
            'appointments' => $appointments,
            'prescriptions' => $user->prescriptions()->with(['doctor', 'hospital', 'chamber'])->get(),
            'reports' => $user->reports()->with(['doctor', 'hospital', 'chamber'])->get(),
        ]);
    }

    public function findOrCreateByPhone(FindOrCreateUserByPhoneRequest $request, FindOrCreateUserByPhoneUseCase $useCase)
    {
        $user = $useCase->execute($request->route('phone'), $request->validated());
        return response()->json([
            'data' => $user,
            'appointments' => [],
            'prescriptions' => [],
            'reports' => [],
        ]);
    }

    public function update($id, UpdateUserRequest $request, UpdateUserUseCase $useCase)
    {
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        return response()->json(
            $useCase->execute($id, $data)
        );
    }

    public function destroy($id, DeleteUserUseCase $useCase)
    {
        $useCase->execute($id);
        return response()->json(['message' => 'User deleted']);
    }
}