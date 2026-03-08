<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use App\Core\CQRS\Query;

class GetCustomersQuery implements Query
{
    public function __construct(
        public readonly ?string $search = null
    ) {
    }
}
