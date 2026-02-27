<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Commands;

use App\Core\CQRS\Command;

class CreateRoleCommand implements Command
{
    public function __construct(
        public readonly string $name,
        /** @var string[] */
        public readonly array $permissionIds = []
    ) {
    }
}
