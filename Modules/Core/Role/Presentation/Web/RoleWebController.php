<?php

declare(strict_types=1);

namespace Modules\Core\Role\Presentation\Web;

use Illuminate\Http\Request;
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
use Modules\Core\Permission\Infrastructure\ReadModels\PermissionGroupReadModel;

class RoleWebController extends Controller
{
    public function index(Request $request, GetRolesPaginatedHandler $handler)
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');

        $query = new GetRolesPaginatedQuery($perPage, $page, $search);
        $roles = $handler->handle($query);

        return view('role::index', compact('roles', 'search'));
    }

    public function create()
    {
        $permissionGroups = PermissionGroupReadModel::with('permissions')
            ->orderBy('sort_order')
            ->get();

        return view('role::create', compact('permissionGroups'));
    }

    public function store(Request $request, CreateRoleHandler $handler)
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

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(string $id, GetRoleByIdHandler $handler)
    {
        $query = new GetRoleByIdQuery($id);
        $role = $handler->handle($query);

        if (!$role) {
            return redirect()->route('admin.roles.index')->with('error', 'Role not found.');
        }

        $permissionGroups = PermissionGroupReadModel::with('permissions')
            ->orderBy('sort_order')
            ->get();

        $assignedPermissionIds = $role->permissions->pluck('id')->toArray();

        return view('role::edit', compact('role', 'permissionGroups', 'assignedPermissionIds'));
    }

    public function update(Request $request, string $id, UpdateRoleHandler $handler)
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
            return redirect()->route('admin.roles.index')->with('error', 'Role not found.');
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(string $id, DeleteRoleHandler $handler)
    {
        try {
            $command = new DeleteRoleCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete this role.');
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
