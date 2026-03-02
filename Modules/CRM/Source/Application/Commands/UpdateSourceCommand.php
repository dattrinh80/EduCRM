<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Application\Commands;

class UpdateSourceCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public string $code,
        public bool $isActive
    ) {}
}
