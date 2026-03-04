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

class UpdateUserHandler implements CommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): User
    {
        /** @var UpdateUserCommand $command */

        $user = $this->repository->findById($command->id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $systemOwnerRoleId = DB::table('roles')->where('name', 'SYSTEM_OWNER')->value('id');
        $hadSystemOwner = UserRoleReadModel::where('user_id', $command->id)->where('role_id', $systemOwnerRoleId)->exists();
        
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
        if (!$hadSystemOwner && $willHaveSystemOwner) {
            if ($currentUserId !== 'SYSTEM' && !$authService->hasPermission($currentUserId, 'MANAGE_SYSTEM_OWNER', 'SYSTEM')) {
                throw new \Exception('Sefeguard Guard: Bạn không có quyền cấp phát chức vụ SYSTEM_OWNER.');
            }
        }

        DB::beginTransaction();
        try {
            $user->update(
                $command->name,
                $command->email,
                $command->password ? Hash::make($command->password) : null,
                $command->defaultCenterId
            );
            $this->repository->update($user);

            // Rule C: Cannot revoke if count <= 1 or no permission
            if ($hadSystemOwner && !$willHaveSystemOwner) {
                if ($currentUserId !== 'SYSTEM' && !$authService->hasPermission($currentUserId, 'MANAGE_SYSTEM_OWNER', 'SYSTEM')) {
                    throw new \Exception('Safeguard Error: Bạn không có quyền thu hồi chức vụ SYSTEM_OWNER.');
                }
                $count = DB::table('user_roles')->where('role_id', $systemOwnerRoleId)->distinct('user_id')->count();
                if ($count <= 1) {
                    throw new \Exception('Safeguard Error: Không được phép thu hồi role SYSTEM_OWNER cuối cùng.');
                }
                SystemAuditLogger::log('REVOKE_SYSTEM_OWNER', $currentUserId, $command->id);
            }

            if (!$hadSystemOwner && $willHaveSystemOwner) {
                SystemAuditLogger::log('ASSIGN_SYSTEM_OWNER', $currentUserId, $command->id);
            }

            UserRoleReadModel::where('user_id', $command->id)->delete();

            foreach ($command->roles as $role) {
                UserRoleReadModel::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $command->id,
                    'role_id' => $role['role_id'],
                    'scope_type' => $role['scope_type'],
                    'scope_id' => $role['scope_id'] ?? null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $user;
    }
}
