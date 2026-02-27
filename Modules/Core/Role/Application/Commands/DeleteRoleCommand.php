<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Commands;

use App\Core\CQRS\Command;

class DeleteRoleCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
