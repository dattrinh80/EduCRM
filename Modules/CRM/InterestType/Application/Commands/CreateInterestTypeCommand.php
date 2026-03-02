<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Application\Commands;

class CreateInterestTypeCommand
{
    public function __construct(
        public string $name,
        public ?string $description
    ) {}
}
