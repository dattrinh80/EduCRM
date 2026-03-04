<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CenterContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Determine raw Super Admin role
        $isSuperAdminRole = false;
        if (method_exists($user, 'hasRole')) {
            $isSuperAdminRole = $user->hasRole('Super Admin');
        }
        $isSuperAdminRole = $isSuperAdminRole || $user->email === 'admin@admin.com' || $user->email === 'admin@educrm.vn' || $user->email === 'admin@eim.vn';

        // Fetch scopes
        $hasGlobalScope = false;
        $allowedCenterIds = [];
        try {
            $authService = app(\Modules\Core\User\Application\Services\AuthorizationServiceInterface::class);
            $hasGlobalScope = $authService->hasGlobalScope($user->id);
            $allowedCenterIds = $authService->getAllowedCenterIds($user->id);
        } catch (\Exception $e) {}

        $hasGlobalScope = $hasGlobalScope || $isSuperAdminRole;

        // Resolve current center_id: session takes priority
        $centerId = session('current_center_id');

        // If no center selected and no global scope, force to first allowed center or default
        if (empty($centerId) && !$hasGlobalScope) {
            if (!empty($allowedCenterIds) && $allowedCenterIds[0] !== 'ALL') {
                $centerId = $allowedCenterIds[0];
            } else {
                $centerId = $user->default_center_id;
            }
            if ($centerId) {
                session(['current_center_id' => $centerId]);
            }
        }

        // A user acts globally ONLY if they have global scope AND haven't selected a specific center
        $isActingGlobally = $hasGlobalScope && empty($centerId);
        
        app()->instance('is_super_admin', $isActingGlobally);

        if (!$centerId && !$isActingGlobally) {
            app()->instance('center_id', null);
        } else {
            app()->instance('center_id', $centerId);
        }

        app()->instance('has_global_scope', $hasGlobalScope);
        app()->instance('allowed_center_ids', $allowedCenterIds);

        return $next($request);
    }
}
