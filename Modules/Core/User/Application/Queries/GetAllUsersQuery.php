<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Queries;

class GetAllUsersQuery
{
    public function __construct(
        public readonly ?string $centerId = null
    ) {}
}
