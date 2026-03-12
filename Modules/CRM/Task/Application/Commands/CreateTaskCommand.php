<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Commands;

use App\Core\CQRS\Command;

class CreateTaskCommand implements Command
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $dueDate,
        public readonly string $priority,
        public readonly ?string $assignedTo,
        public readonly string $assignedBy,
        public readonly string $centerId,
        public readonly ?string $startDate = null,
        public readonly ?string $relationId = null,
        public readonly ?string $relationType = null
    ) {
    }
}
