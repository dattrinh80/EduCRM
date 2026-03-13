<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Queries;

use App\Core\CQRS\Query;

class ExportStudentsQuery implements Query
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly string $format = 'xlsx' // xlsx, csv, pdf
    ) {}
}
