<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Commands;

use App\Core\CQRS\Command;

class LogSystemOwnerLoginCommand implements Command
{
    public function __construct(
        public readonly string $userId,
        public readonly string $targetId
    ) {}
}
