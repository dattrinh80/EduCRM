<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\User\Domain\User;
use Modules\Core\User\Domain\UserRepositoryInterface;
use Modules\Core\User\Infrastructure\ReadModels\UserRoleReadModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Modules\Core\User\Application\Services\AuthorizationServiceInterface;
use Modules\Core\User\Infrastructure\Services\SystemAuditLogger;

class CreateUserHandler implements CommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): User
    {
        /** @var CreateUserCommand $command */

        $systemOwnerRoleId = DB::table('roles')->where('name', 'SYSTEM_OWNER')->value('id');
        $willHaveSystemOwner = false;
        foreach ($command->roles as $role) {
            if ($role['role_id'] === $systemOwnerRoleId) {
                $willHaveSystemOwner = true;
                break;
            }
        }

        $currentUserId = auth()->id() ?? 'SYSTEM';
        $authService = app(AuthorizationServiceInterface::class);

        // Rule D: Only MANAGE_SYSTEM_OWNER can assign SYSTEM_OWNER
        if ($willHaveSystemOwner) {
            if ($currentUserId !== 'SYSTEM' && !$authService->hasPermission($currentUserId, 'MANAGE_SYSTEM_OWNER', 'SYSTEM')) {
                throw new \Exception('Sefeguard Guard: Bạn không có quyền cấp phát chức vụ SYSTEM_OWNER.');
            }
        }

        DB::beginTransaction();
        try {
            $user = User::create(
                (string) Str::uuid(),
                $command->name,
                $command->email,
                Hash::make($command->password),
                $command->defaultCenterId
            );

            $this->repository->save($user);

            // Assign roles with scopes
            foreach ($command->roles as $role) {
                UserRoleReadModel::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->getId(),
                    'role_id' => $role['role_id'],
                    'scope_type' => $role['scope_type'],
                    'scope_id' => $role['scope_id'] ?? null,
                ]);
            }

            if ($willHaveSystemOwner) {
                SystemAuditLogger::log('ASSIGN_SYSTEM_OWNER', $currentUserId, $user->getId());
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $user;
    }
}
