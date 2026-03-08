<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Domain;

interface TaskRepositoryInterface
{
    public function findById(string $id): ?Task;
    public function save(Task $task): void;
    public function update(Task $task): void;
    public function delete(string $id): void;
}
