<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

class BulkUpdateLeadsCommand
{
    public function __construct(
        public array $leadIds,
        public ?string $sourceId = null,
        public ?string $interestTypeId = null,
        public ?string $centerId = null,
        public ?string $assignedTo = null,
        public ?string $campaignId = null,
        public ?string $status = null,
    ) {}
}
