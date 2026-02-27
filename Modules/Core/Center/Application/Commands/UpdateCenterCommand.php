<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Commands;

use App\Core\CQRS\Command;

class UpdateCenterCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $status,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $address = null
    ) {
    }
}
