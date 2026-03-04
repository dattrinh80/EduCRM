<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\Services;

use Modules\Core\User\Application\Services\AuthorizationServiceInterface;
use Illuminate\Support\Facades\DB;

class DatabaseAuthorizationService implements AuthorizationServiceInterface
{
    public function can(string $userId, string $permission): bool
    {
        // 1. Root admin bypass: Always has full access
        $userEmail = DB::table('users')->where('id', $userId)->value('email');
        if (in_array($userEmail, ['admin@admin.com', 'admin@educrm.vn', 'admin@eim.vn'])) {
            return true;
        }

        // 2. Determine current UI context
        $isGlobalContext = false;
        try { $isGlobalContext = app('is_super_admin'); } catch (\Exception $e) {}

        $currentCenterId = null;
        try { $currentCenterId = app('center_id'); } catch (\Exception $e) {}

        // 3. Evaluate permissions based strictly on the current context scale
        $query = DB::table('user_roles')
            ->join('role_permissions', 'user_roles.role_id', '=', 'role_permissions.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('user_roles.user_id', $userId)
            ->where('permissions.name', $permission);

        if ($isGlobalContext) {
            // UI Context: System -> Activate ONLY Global Scopes
            $query->where('user_roles.scope_type', 'SYSTEM');
        } elseif ($currentCenterId) {
            // UI Context: Center -> Activate ONLY Center Scopes (Do NOT inherit global)
            $query->where('user_roles.scope_type', 'CENTER')
                  ->where('user_roles.scope_id', $currentCenterId);
        } else {
            // Unknown or incomplete context -> Deny
            return false;
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
        
        $userEmail = DB::table('users')->where('id', $userId)->value('email');
        if (in_array($userEmail, ['admin@admin.com', 'admin@educrm.vn', 'admin@eim.vn'])) {
            $allCenterIds = DB::table('centers')->where('status', 'active')->pluck('id')->toArray();
            return array_unique(array_merge(['SYSTEM'], $allCenterIds));
        }

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
