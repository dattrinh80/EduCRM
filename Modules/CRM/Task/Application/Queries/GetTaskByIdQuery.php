<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Queries;

use App\Core\CQRS\Query;

class GetTaskByIdQuery implements Query
{
    public function __construct(public readonly string $id)
    {
    }
}
