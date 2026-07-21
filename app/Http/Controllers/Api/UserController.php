<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Application\UseCases\User\{
    CreateUserUseCase,
    GetAllUsersUseCase,
    GetUserUseCase,
    UpdateUserUseCase,
    DeleteUserUseCase
};
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;

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