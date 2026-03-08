<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\Command;

class UpdateLeadStatusCommand implements Command
{
    public function __construct(
        public readonly string $leadId,
        public readonly string $statusId,
        public readonly string $updatedBy
    ) {
    }
}
