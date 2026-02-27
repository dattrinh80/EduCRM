<?php

declare(strict_types=1);

namespace Modules\Core\User\Domain;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function update(User $user): void;
    public function delete(string $id): void;
    public function findById(string $id): ?User;
}
