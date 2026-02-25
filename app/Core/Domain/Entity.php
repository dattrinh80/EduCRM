<?php

declare(strict_types=1);

namespace App\Core\Domain;

abstract class Entity
{
    protected string $id;

    public function getId(): string
    {
        return $this->id;
    }
}
