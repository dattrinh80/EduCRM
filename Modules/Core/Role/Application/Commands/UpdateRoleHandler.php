<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\Role\Infrastructure\ReadModels\RoleReadModel;

class UpdateRoleHandler implements CommandHandler
{
    public function handle(Command $command): RoleReadModel
    {
        /** @var UpdateRoleCommand $command */

        $role = RoleReadModel::findOrFail($command->id);
        $role->update(['name' => $command->name]);
        $role->permissions()->sync($command->permissionIds);

        return $role;
    }
}
