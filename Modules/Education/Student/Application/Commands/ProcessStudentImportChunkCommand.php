<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Commands;

use App\Core\CQRS\Command;

class ProcessStudentImportChunkCommand implements Command
{
    public function __construct(
        public readonly string $importId,
        public readonly int $offset,
        public readonly int $limit = 50,
        public readonly ?string $centerId = null
    ) {}
}
