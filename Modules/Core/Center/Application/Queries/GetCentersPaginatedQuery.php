<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Queries;

use App\Core\CQRS\Query;

class GetCentersPaginatedQuery implements Query
{
    public function __construct(
        public readonly int $perPage = 15,
        public readonly int $page = 1
    ) {
    }
}
