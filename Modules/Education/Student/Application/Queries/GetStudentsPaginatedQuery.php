<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Queries;

use App\Core\CQRS\Query;

class GetStudentsPaginatedQuery implements Query
{
    public function __construct(
        public readonly int $perPage = 15,
        public readonly int $page = 1,
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $sortDirection = null
    ) {}
}
