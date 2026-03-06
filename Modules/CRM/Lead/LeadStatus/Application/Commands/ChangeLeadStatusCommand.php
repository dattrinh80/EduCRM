<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadStatus\Application\Commands;

class ChangeLeadStatusCommand
{
    public function __construct(
        public readonly string $leadId,
        public readonly string $statusId,
        public readonly ?string $changedBy = null
    ) {}
}
