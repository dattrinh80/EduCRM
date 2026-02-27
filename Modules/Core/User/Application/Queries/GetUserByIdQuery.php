<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Queries;

use App\Core\CQRS\Query;

class GetUserByIdQuery implements Query
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
