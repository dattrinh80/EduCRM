<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Queries;

class GetLeadTagsQuery
{
    public function __construct(
        public ?string $search = null
    ) {}
}
