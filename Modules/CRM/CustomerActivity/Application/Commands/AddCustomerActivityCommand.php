<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerActivity\Application\Commands;

class AddCustomerActivityCommand
{
    public function __construct(
        public readonly string $customerId,
        public readonly string $activityType,
        public readonly ?string $description = null,
        public readonly ?string $createdBy = null
    ) {}
}

