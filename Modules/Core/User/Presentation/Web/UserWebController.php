<?php

declare(strict_types=1);

namespace Modules\Core\User\Presentation\Web;

use Illuminate\Http\Request;
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
use Modules\Core\User\Infrastructure\ReadModels\RoleReadModel;

class UserWebController extends Controller
{
    public function index(Request $request, GetUsersPaginatedHandler $handler)
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');
        $roleId = $request->query('role_id');

        $query = new GetUsersPaginatedQuery($perPage, $page, $search, $roleId);
        $users = $handler->handle($query);

        $roles = RoleReadModel::orderBy('name')->get();

        return view('user::index', compact('users', 'roles', 'search', 'roleId'));
    }

    public function create()
    {
        $roles = RoleReadModel::orderBy('name')->get();

        return view('user::create', compact('roles'));
    }

    public function store(Request $request, CreateUserHandler $handler)
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

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(string $id, GetUserByIdHandler $handler)
    {
        $query = new GetUserByIdQuery($id);
        $user = $handler->handle($query);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        $roles = RoleReadModel::orderBy('name')->get();

        return view('user::edit', compact('user', 'roles'));
    }

    public function update(Request $request, string $id, UpdateUserHandler $handler)
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
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(string $id, DeleteUserHandler $handler)
    {
        try {
            $command = new DeleteUserCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
