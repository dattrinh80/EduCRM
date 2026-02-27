<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Queries;

use App\Core\CQRS\Query;

class GetCenterByIdQuery implements Query
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
