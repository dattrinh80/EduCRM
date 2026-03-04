<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Services;

interface AuthorizationServiceInterface
{
    public function can(string $userId, string $permission): bool;
    public function hasPermission(string $userId, string $permission, string $scopeLevel = 'SYSTEM', ?string $scopeId = null): bool;
    public function hasGlobalScope(string $userId): bool;
    public function getAllowedCenterIds(string $userId): array;
}
