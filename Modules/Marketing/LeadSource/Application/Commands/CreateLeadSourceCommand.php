<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Application\Commands;

class CreateLeadSourceCommand
{
    public function __construct(
        public string $name,
        public string $code
    ) {}
}
