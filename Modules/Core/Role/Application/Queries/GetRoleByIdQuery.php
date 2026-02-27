<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Queries;

use App\Core\CQRS\Query;

class GetRoleByIdQuery implements Query
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
