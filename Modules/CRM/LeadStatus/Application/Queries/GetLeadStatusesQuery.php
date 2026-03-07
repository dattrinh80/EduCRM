<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Application\Queries;

class GetLeadStatusesQuery
{
    public function __construct(
        public ?string $search = null,
        public bool $onlyActive = false
    ) {}
}
