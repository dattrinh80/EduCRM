<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Queries;

class GetCustomerTagsQuery
{
    public function __construct(
        public ?string $search = null
    ) {}
}
