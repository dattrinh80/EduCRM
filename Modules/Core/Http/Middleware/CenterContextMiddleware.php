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

        // Fetch scopes
        $hasGlobalScope = false;
        $allowedCenterIds = [];
        try {
            $authService = app(\Modules\Core\User\Application\Services\AuthorizationServiceInterface::class);
            $hasGlobalScope = $authService->hasGlobalScope($user->id);
            $allowedCenterIds = $authService->getAllowedCenterIds($user->id);
        } catch (\Exception $e) {}

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
                session(['active_scope_level' => 'CENTER', 'active_scope_id' => $centerId]);
            }
        }
        
        // Sync active scope variables if they somehow went missing but centerId didn't
        if (!session()->has('active_scope_level')) {
            if (empty($centerId) && $hasGlobalScope) {
                 session(['active_scope_level' => 'SYSTEM', 'active_scope_id' => null]);
            } elseif ($centerId) {
                 session(['active_scope_level' => 'CENTER', 'active_scope_id' => $centerId]);
            }
        }

        // A user acts globally ONLY if they have global scope AND haven't selected a specific center
        $isActingGlobally = $hasGlobalScope && empty($centerId);
        
        app()->instance('is_global_scope', $isActingGlobally);

        if (!$centerId && !$isActingGlobally) {
            app()->instance('center_id', null);
        } else {
            app()->instance('center_id', $centerId);
        }

        app()->instance('has_global_scope', $hasGlobalScope);
        app()->instance('allowed_center_ids', $allowedCenterIds);

        // Fetch current roles for the active scope
        $currentRoles = [];
        try {
            $scopeLevel = session('active_scope_level', 'SYSTEM');
            $scopeId = session('active_scope_id');
            $currentRoles = $authService->getCurrentScopeRoles($user->id, $scopeLevel, $scopeId);
        } catch (\Exception $e) {}
        app()->instance('current_scope_roles', $currentRoles);

        return $next($request);
    }
}
