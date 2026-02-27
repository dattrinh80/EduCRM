<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Services;

interface AuthorizationServiceInterface
{
    public function can(string $userId, string $permission): bool;
    public function hasGlobalScope(string $userId): bool;
    public function getAllowedCenterIds(string $userId): array;
}
