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

        // Check if user is Super Admin
        $isSuperAdmin = false;
        if (method_exists($user, 'hasRole')) {
            $isSuperAdmin = $user->hasRole('Super Admin');
        }
        $isSuperAdmin = $isSuperAdmin || $user->email === 'admin@admin.com';
        
        app()->instance('is_super_admin', $isSuperAdmin);

        // Resolve current center_id: session takes priority, then default_center_id
        $centerId = session('current_center_id', $user->default_center_id);

        if (!$centerId && !$isSuperAdmin) {
            // Normal users without a center context cannot see any center-scoped data
            app()->instance('center_id', null);
        } else {
            app()->instance('center_id', $centerId);
            
            // Persist to session if not already there
            if (!session()->has('current_center_id') && $centerId) {
                session(['current_center_id' => $centerId]);
            }
        }

        return $next($request);
    }
}
