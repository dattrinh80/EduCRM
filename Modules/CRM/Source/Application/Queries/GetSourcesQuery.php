<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Application\Queries;

class GetSourcesQuery
{
    public function __construct(
        public ?string $search = null,
        public ?bool $isActive = null
    ) {}
}
