<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Commands;

use App\Core\CQRS\Command;

class CreateCenterCommand implements Command
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $address = null
    ) {
    }
}
