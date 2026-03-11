<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

class ImportCustomerCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?string $email = null,
        public readonly ?string $centerCode = null,
        public readonly ?string $dob = null,
        public readonly ?string $gender = null,
        public readonly ?string $address = null
    ) {
    }
}
