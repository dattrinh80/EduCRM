<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\Role\Infrastructure\ReadModels\RoleReadModel;
use Illuminate\Support\Facades\DB;

class DeleteRoleHandler implements CommandHandler
{
    public function handle(Command $command): mixed
    {
        /** @var DeleteRoleCommand $command */

        $role = RoleReadModel::findOrFail($command->id);

        if ($role->is_system_role) {
            throw new \Exception('Safeguard Error: Root Governance Role (is_system_role) cannot be deleted.');
        }

        // Detach all permissions first
        $role->permissions()->detach();

        // Remove user_roles assignments
        DB::table('user_roles')->where('role_id', $command->id)->delete();

        $role->delete();

        return null;
    }
}
