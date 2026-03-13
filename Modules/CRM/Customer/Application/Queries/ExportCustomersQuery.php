<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

class ExportCustomersQuery
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $phone = null,
        public readonly ?string $centerId = null,
        public readonly string $format = 'excel'
    ) {}
}
