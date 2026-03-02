<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Application\Commands;

class DeleteSourceCommand
{
    public function __construct(
        public string $id
    ) {}
}
