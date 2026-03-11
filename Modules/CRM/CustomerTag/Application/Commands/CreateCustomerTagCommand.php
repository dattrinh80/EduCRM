<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Commands;

class CreateCustomerTagCommand
{
    public function __construct(
        public string $name,
        public ?string $color = 'slate'
    ) {}
}
