<?php

declare(strict_types=1);

namespace Modules\Core\Role\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Role\Application\Commands\CreateRoleCommand;
use Modules\Core\Role\Application\Commands\CreateRoleHandler;
use Modules\Core\Role\Application\Commands\UpdateRoleCommand;
use Modules\Core\Role\Application\Commands\UpdateRoleHandler;
use Modules\Core\Role\Application\Commands\DeleteRoleCommand;
use Modules\Core\Role\Application\Commands\DeleteRoleHandler;
use Modules\Core\Role\Application\Queries\GetRoleByIdQuery;
use Modules\Core\Role\Application\Queries\GetRoleByIdHandler;
use Modules\Core\Role\Application\Queries\GetRolesPaginatedQuery;
use Modules\Core\Role\Application\Queries\GetRolesPaginatedHandler;

class RoleApiController extends Controller
{
    public function index(Request $request, GetRolesPaginatedHandler $handler): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');

        $query = new GetRolesPaginatedQuery($perPage, $page, $search);
        $roles = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $roles->items(),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total()
            ]
        ]);
    }

    public function show(string $id, GetRoleByIdHandler $handler): JsonResponse
    {
        $query = new GetRoleByIdQuery($id);
        $role = $handler->handle($query);

        if (!$role) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ROLE_NOT_FOUND',
                    'message' => 'Role not found'
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    public function store(Request $request, CreateRoleHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'uuid|exists:permissions,id',
        ]);

        $command = new CreateRoleCommand(
            $validated['name'],
            $validated['permissions'] ?? []
        );

        $handler->handle($command);

        return response()->json([
            'success' => true,
            'data' => []
        ], 201);
    }

    public function update(Request $request, string $id, UpdateRoleHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'uuid|exists:permissions,id',
        ]);

        try {
            $command = new UpdateRoleCommand(
                $id,
                $validated['name'],
                $validated['permissions'] ?? []
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ROLE_ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function destroy(string $id, DeleteRoleHandler $handler): JsonResponse
    {
        try {
            $command = new DeleteRoleCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ROLE_ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}
