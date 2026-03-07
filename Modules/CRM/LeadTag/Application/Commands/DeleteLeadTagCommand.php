<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Commands;

class DeleteLeadTagCommand
{
    public function __construct(
        public string $id
    ) {}
}
