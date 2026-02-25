<?php

declare(strict_types=1);

namespace App\Core\CQRS;

interface QueryHandler
{
    public function handle(Query $query): mixed;
}
