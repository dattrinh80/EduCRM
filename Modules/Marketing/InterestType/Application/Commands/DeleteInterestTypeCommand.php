<?php

declare(strict_types=1);

namespace Modules\Marketing\InterestType\Application\Commands;

class DeleteInterestTypeCommand
{
    public function __construct(
        public string $id
    ) {}
}
