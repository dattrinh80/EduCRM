<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Queries;

use App\Core\CQRS\Query;

class GetLeadsForKanbanQuery implements Query
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $phone = null,
        public readonly ?string $centerId = null,
        public readonly ?string $statusId = null
    ) {
    }
}
