<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Commands;

use App\Core\CQRS\Command;

class ToggleTaskStatusCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
