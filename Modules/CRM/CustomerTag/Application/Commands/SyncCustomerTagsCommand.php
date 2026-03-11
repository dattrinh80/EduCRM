<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Commands;

class SyncCustomerTagsCommand
{
    public function __construct(
        public string $customerId,
        public array $tagIds
    ) {}
}

