<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerNote\Application\Queries;

class GetCustomerNotesQuery
{
    public function __construct(
        public readonly string $customerId,
        public readonly int $perPage = 20,
        public readonly int $page = 1
    ) {}
}

