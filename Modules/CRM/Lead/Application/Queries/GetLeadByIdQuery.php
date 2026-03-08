<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Queries;

use App\Core\CQRS\Query;

class GetLeadByIdQuery implements Query
{
    public function __construct(
        public readonly string $id,
        /** @var array<string> $with */
        public readonly array $with = []
    ) {
    }
}
