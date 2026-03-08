<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Queries;

use App\Core\CQRS\Query;

class GetTasksPaginatedQuery implements Query
{
    public function __construct(
        public readonly int $perPage = 20,
        public readonly int $page = 1,
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly ?string $priority = null,
        public readonly ?string $assignedTo = null,
        public readonly ?string $centerId = null,
        public readonly ?string $relationId = null,
        public readonly ?string $relationType = null
    ) {
    }
}
