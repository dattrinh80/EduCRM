<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Commands;

class UpdateLeadTagCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $color = 'slate'
    ) {}
}
