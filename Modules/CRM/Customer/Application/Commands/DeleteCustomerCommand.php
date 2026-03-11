<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use App\Core\CQRS\Command;

class DeleteCustomerCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
