<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use App\Core\CQRS\Query;

class GetCustomersPaginatedQuery implements Query
{
    public function __construct(
        public readonly int $perPage = 15,
        public readonly int $page = 1,
        public readonly ?string $search = null,
        public readonly ?string $phone = null,
        public readonly ?string $centerId = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $sortDirection = 'desc'
    ) {
    }
}
