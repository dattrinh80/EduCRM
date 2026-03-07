<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Application\Commands;

class UpdateLeadStatusCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public string $stage,
        public int $sortOrder = 0,
        public bool $isActive = true,
        public ?string $color = null
    ) {}
}
