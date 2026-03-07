<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Application\Queries;

class GetLeadSourcesQuery
{
    public function __construct(
        public ?string $search = null,
        public ?bool $isActive = null
    ) {}
}
