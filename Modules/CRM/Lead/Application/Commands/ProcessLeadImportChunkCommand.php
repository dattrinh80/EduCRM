<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\Command;

class ProcessLeadImportChunkCommand implements Command
{
    public function __construct(
        public readonly string $importId,
        public readonly int $offset = 0,
        public readonly int $limit = 10
    ) {}
}
