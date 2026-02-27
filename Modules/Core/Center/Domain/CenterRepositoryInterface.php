<?php

declare(strict_types=1);

namespace Modules\Core\Center\Domain;

interface CenterRepositoryInterface
{
    public function save(Center $center): void;

    public function update(Center $center): void;

    public function delete(string $id): void;

    public function findById(string $id): ?Center;
}
