<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Queries;

use App\Core\CQRS\Query;

class AuthenticateUserQuery implements Query
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $deviceName = null
    ) {
    }
}
