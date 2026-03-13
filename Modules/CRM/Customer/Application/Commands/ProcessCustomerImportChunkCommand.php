<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

class ProcessCustomerImportChunkCommand
{
    public function __construct(
        public readonly string $importId,
        public readonly int $offset = 0,
        public readonly int $limit = 10
    ) {}
}
