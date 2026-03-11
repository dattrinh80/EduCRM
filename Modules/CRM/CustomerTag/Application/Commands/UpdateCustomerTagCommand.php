<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Commands;

class UpdateCustomerTagCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $color = 'slate'
    ) {}
}
