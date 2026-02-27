<?php

declare(strict_types=1);

namespace Modules\Core\User\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\User\Application\Commands\CreateUserCommand;
use Modules\Core\User\Application\Commands\CreateUserHandler;
use Modules\Core\User\Application\Commands\UpdateUserCommand;
use Modules\Core\User\Application\Commands\UpdateUserHandler;
use Modules\Core\User\Application\Commands\DeleteUserCommand;
use Modules\Core\User\Application\Commands\DeleteUserHandler;
use Modules\Core\User\Application\Queries\GetUserByIdQuery;
use Modules\Core\User\Application\Queries\GetUserByIdHandler;
use Modules\Core\User\Application\Queries\GetUsersPaginatedQuery;
use Modules\Core\User\Application\Queries\GetUsersPaginatedHandler;

class UserApiController extends Controller
{
    public function index(Request $request, GetUsersPaginatedHandler $handler): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');
        $roleId = $request->query('role_id');

        $query = new GetUsersPaginatedQuery($perPage, $page, $search, $roleId);
        $users = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total()
            ]
        ]);
    }

    public function show(string $id, GetUserByIdHandler $handler): JsonResponse
    {
        $query = new GetUserByIdQuery($id);
        $user = $handler->handle($query);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'User not found'
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function store(Request $request, CreateUserHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'roles.*.role_id' => 'required|uuid|exists:roles,id',
            'roles.*.scope_type' => 'required|string|in:ALL,CENTER',
            'roles.*.scope_id' => 'nullable|uuid',
        ]);

        $command = new CreateUserCommand(
            $validated['name'],
            $validated['email'],
            $validated['password'],
            $validated['roles'] ?? []
        );

        $handler->handle($command);

        return response()->json([
            'success' => true,
            'data' => []
        ], 201);
    }

    public function update(Request $request, string $id, UpdateUserHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'roles.*.role_id' => 'required|uuid|exists:roles,id',
            'roles.*.scope_type' => 'required|string|in:ALL,CENTER',
            'roles.*.scope_id' => 'nullable|uuid',
        ]);

        try {
            $command = new UpdateUserCommand(
                $id,
                $validated['name'],
                $validated['email'],
                $validated['password'] ?? null,
                $validated['roles'] ?? []
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'USER_ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function destroy(string $id, DeleteUserHandler $handler): JsonResponse
    {
        try {
            $command = new DeleteUserCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => $e->getMessage()
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}
