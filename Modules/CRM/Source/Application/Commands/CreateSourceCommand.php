<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Application\Commands;

class CreateSourceCommand
{
    public function __construct(
        public string $name,
        public string $code
    ) {}
}
