<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Commands;

class DeleteCustomerTagCommand
{
    public function __construct(
        public string $id
    ) {}
}
