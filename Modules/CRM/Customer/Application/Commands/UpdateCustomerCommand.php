<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use App\Core\CQRS\Command;

class UpdateCustomerCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $dob = null,
        public readonly ?string $gender = null,
        public readonly ?string $address = null,
        public readonly ?string $centerId = null,
        public readonly array $tagIds = []
    ) {
    }
}
