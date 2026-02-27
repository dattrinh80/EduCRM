<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Queries;

use App\Core\CQRS\Query;

class GetRolesPaginatedQuery implements Query
{
    public function __construct(
        public readonly int $perPage = 15,
        public readonly int $page = 1,
        public readonly ?string $search = null
    ) {
    }
}
