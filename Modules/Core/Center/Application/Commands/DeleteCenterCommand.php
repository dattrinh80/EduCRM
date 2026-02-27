<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Commands;

use App\Core\CQRS\Command;

class DeleteCenterCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
