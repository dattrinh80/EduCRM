<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\Services;

use Modules\Core\User\Application\Services\AuthorizationServiceInterface;
use Illuminate\Support\Facades\DB;

class DatabaseAuthorizationService implements AuthorizationServiceInterface
{
    public function can(string $userId, string $permission): bool
    {
        return DB::table('user_roles')
            ->join('role_permissions', 'user_roles.role_id', '=', 'role_permissions.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('user_roles.user_id', $userId)
            ->where('permissions.name', $permission)
            ->exists();
    }

    public function hasGlobalScope(string $userId): bool
    {
        return DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('scope_type', 'ALL')
            ->exists();
    }

    public function getAllowedCenterIds(string $userId): array
    {
        if ($this->hasGlobalScope($userId)) {
            return ['ALL'];
        }

        return DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('scope_type', 'CENTER')
            ->whereNotNull('scope_id')
            ->pluck('scope_id')
            ->toArray();
    }
}
