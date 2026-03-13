<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Queries;

use App\Core\CQRS\Query;

class GetStudentByIdQuery implements Query
{
    public function __construct(
        public readonly string $id,
        public readonly array $with = ['customer', 'customer.center', 'guardians']
    ) {}
}
