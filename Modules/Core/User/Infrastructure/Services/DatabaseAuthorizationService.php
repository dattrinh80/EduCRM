<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\Services;

use Modules\Core\User\Application\Services\AuthorizationServiceInterface;
use Illuminate\Support\Facades\DB;

class DatabaseAuthorizationService implements AuthorizationServiceInterface
{
    public function can(string $userId, string $permission): bool
    {
        $activeScopeLevel = session('active_scope_level', 'SYSTEM');
        $activeScopeId = session('active_scope_id');

        // Fallback for API or commands that might set context manually via app instance
        if (!$activeScopeId) {
            try { $activeScopeId = app('center_id'); } catch (\Exception $e) {}
        }
        
        if ($activeScopeId && $activeScopeLevel === 'SYSTEM') {
             // Safe guard, if SYSTEM but somehow there is a local ID (shouldn't happen but defensive)
            $activeScopeId = null;
        }

        return $this->hasPermission($userId, $permission, $activeScopeLevel, $activeScopeId);
    }

    public function hasPermission(string $userId, string $permission, string $scopeLevel = 'SYSTEM', ?string $scopeId = null): bool
    {
        $query = DB::table('user_roles')
            ->join('role_permissions', 'user_roles.role_id', '=', 'role_permissions.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('user_roles.user_id', $userId)
            ->where('permissions.name', $permission)
            ->where('user_roles.scope_type', $scopeLevel);

        if ($scopeLevel !== 'SYSTEM' && $scopeId !== null) {
            $query->where('user_roles.scope_id', $scopeId);
        }

        return $query->exists();
    }

    public function hasGlobalScope(string $userId): bool
    {
        return DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('scope_type', 'SYSTEM')
            ->exists();
    }

    public function getAllowedCenterIds(string $userId): array
    {
        $scopes = [];

        if ($this->hasGlobalScope($userId)) {
            $scopes[] = 'SYSTEM';
        }

        $centerScopes = DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('scope_type', 'CENTER')
            ->whereNotNull('scope_id')
            ->pluck('scope_id')
            ->toArray();

        return array_unique(array_merge($scopes, $centerScopes));
    }
}
