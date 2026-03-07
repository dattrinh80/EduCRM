<?php

declare(strict_types=1);

namespace Modules\Marketing\InterestType\Application\Commands;

class CreateInterestTypeCommand
{
    public function __construct(
        public string $name,
        public ?string $description
    ) {}
}
