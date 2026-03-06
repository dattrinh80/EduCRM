<?php

declare(strict_types=1);

namespace Modules\CRM\LeadActivity\Application\Queries;

class GetLeadActivitiesQuery
{
    public function __construct(
        public readonly string $leadId,
        public readonly int $perPage = 20,
        public readonly int $page = 1
    ) {}
}

