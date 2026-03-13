<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Commands;

use App\Core\CQRS\Command;

class DeleteStudentCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {}
}
