<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Application\Commands;

class UpdateLeadSourceCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public string $code,
        public bool $isActive
    ) {}
}
