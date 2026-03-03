<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\Command;

class MergeLeadsCommand implements Command
{
    public function __construct(
        public readonly string $masterLeadId,
        public readonly array $slaveLeadIds
    ) {
    }
}
