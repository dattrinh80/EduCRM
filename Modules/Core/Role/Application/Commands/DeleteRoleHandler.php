<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\Role\Infrastructure\ReadModels\RoleReadModel;
use Illuminate\Support\Facades\DB;

class DeleteRoleHandler implements CommandHandler
{
    public function handle(Command $command): void
    {
        /** @var DeleteRoleCommand $command */

        $role = RoleReadModel::findOrFail($command->id);

        // Detach all permissions first
        $role->permissions()->detach();

        // Remove user_roles assignments
        DB::table('user_roles')->where('role_id', $command->id)->delete();

        $role->delete();
    }
}
