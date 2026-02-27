<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\Role\Infrastructure\ReadModels\RoleReadModel;
use Illuminate\Support\Str;

class CreateRoleHandler implements CommandHandler
{
    public function handle(Command $command): RoleReadModel
    {
        /** @var CreateRoleCommand $command */

        $role = RoleReadModel::create([
            'id' => (string) Str::uuid(),
            'name' => $command->name,
        ]);

        if (!empty($command->permissionIds)) {
            $role->permissions()->sync($command->permissionIds);
        }

        return $role;
    }
}
