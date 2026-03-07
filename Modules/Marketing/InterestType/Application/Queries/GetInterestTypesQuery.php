<?php

declare(strict_types=1);

namespace Modules\Marketing\InterestType\Application\Queries;

class GetInterestTypesQuery
{
    public function __construct(
        public ?string $search = null,
        public ?bool $isActive = null
    ) {}
}
