<?php

declare(strict_types=1);

namespace App\Core\CQRS;

interface CommandHandler
{
    public function handle(Command $command): mixed;
}
