<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Application\Commands;

class UpdateInterestTypeCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public bool $isActive
    ) {}
}
