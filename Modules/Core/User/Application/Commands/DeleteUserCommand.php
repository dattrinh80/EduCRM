<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Commands;

use App\Core\CQRS\Command;

class DeleteUserCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
