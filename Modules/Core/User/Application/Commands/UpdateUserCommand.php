<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Commands;

use App\Core\CQRS\Command;

class UpdateUserCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly ?string $defaultCenterId = null,
        /** @var array<array{role_id: string, scope_type: string, scope_id: ?string}> */
        public readonly array $roles = []
    ) {
    }
}
