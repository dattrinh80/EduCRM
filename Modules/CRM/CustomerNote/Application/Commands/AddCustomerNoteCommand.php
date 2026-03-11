<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerNote\Application\Commands;

class AddCustomerNoteCommand
{
    public function __construct(
        public readonly string $customerId,
        public readonly string $content,
        public readonly ?string $createdBy = null
    ) {}
}

