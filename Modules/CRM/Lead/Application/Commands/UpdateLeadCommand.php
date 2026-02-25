<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\Command;

class UpdateLeadCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $phone,
        public readonly string $status,
        public readonly ?string $email = null,
        public readonly ?string $centerId = null
    ) {
    }
}
