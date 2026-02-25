<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\Command;

class DeleteLeadCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
