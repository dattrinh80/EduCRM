<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerActivity\Application\Queries;

class GetCustomerActivitiesQuery
{
    public function __construct(
        public readonly string $customerId,
        public readonly int $perPage = 20,
        public readonly int $page = 1
    ) {}
}

