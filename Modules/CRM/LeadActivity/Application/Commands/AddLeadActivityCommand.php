<?php

declare(strict_types=1);

namespace Modules\CRM\LeadActivity\Application\Commands;

class AddLeadActivityCommand
{
    public function __construct(
        public readonly string $leadId,
        public readonly string $activityType,
        public readonly ?string $description = null,
        public readonly ?string $createdBy = null
    ) {}
}

