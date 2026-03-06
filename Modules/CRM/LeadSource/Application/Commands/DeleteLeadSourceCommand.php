<?php

declare(strict_types=1);

namespace Modules\CRM\LeadSource\Application\Commands;

class DeleteLeadSourceCommand
{
    public function __construct(
        public string $id
    ) {}
}
