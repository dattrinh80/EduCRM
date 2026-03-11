<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use App\Core\CQRS\Query;

class GetCustomerByIdQuery implements Query
{
    public function __construct(
        public readonly string $id,
        public readonly array $relations = []
    ) {
    }
}
