<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Queries;

use App\Core\CQRS\Query;

class SearchTaskRelationsQuery implements Query
{
    public function __construct(
        public readonly string $query,
        public readonly ?string $type = null
    ) {
    }
}
