<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Application\Commands;

class DeleteLeadStatusCommand
{
    public function __construct(
        public string $id
    ) {}
}
