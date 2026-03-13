<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Commands;

use App\Core\CQRS\Command;

class UpdateStudentCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $name = null,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $status = null,
        public readonly ?string $dob = null,
        public readonly ?string $gender = null,
        public readonly ?string $address = null
    ) {}
}
