<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Commands;

class CreateLeadTagCommand
{
    public function __construct(
        public string $name,
        public ?string $color = 'slate'
    ) {}
}
