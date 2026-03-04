<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\User\Domain\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Modules\Core\User\Infrastructure\Services\SystemAuditLogger;

class DeleteUserHandler implements CommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): mixed
    {
        /** @var DeleteUserCommand $command */

        $user = $this->repository->findById($command->id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $systemOwnerRoleId = DB::table('roles')->where('name', 'SYSTEM_OWNER')->value('id');
        $hadSystemOwner = DB::table('user_roles')->where('user_id', $command->id)->where('role_id', $systemOwnerRoleId)->exists();
        
        $currentUserId = auth()->id() ?? 'SYSTEM';
        $authService = app(\Modules\Core\User\Application\Services\AuthorizationServiceInterface::class);
        
        DB::beginTransaction();
        try {
            if ($hadSystemOwner) {
                if ($currentUserId !== 'SYSTEM' && !$authService->hasPermission($currentUserId, 'MANAGE_SYSTEM_OWNER', 'SYSTEM')) {
                    throw new \Exception('Safeguard Error: Root User (SYSTEM_OWNER) không thể bị xoá bởi người dùng không có thẩm quyền.');
                }
                
                // Must count without this user
                $count = DB::table('user_roles')->where('role_id', $systemOwnerRoleId)->where('user_id', '!=', $command->id)->distinct('user_id')->count();
                if ($count < 1) {
                    throw new \Exception('Safeguard Error: Không được phép xoá user mang SYSTEM_OWNER cuối cùng.');
                }
                
                SystemAuditLogger::log('DELETE_SYSTEM_OWNER_USER', $currentUserId, $command->id);
            }

            $this->repository->delete($command->id);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return null;
    }
}
