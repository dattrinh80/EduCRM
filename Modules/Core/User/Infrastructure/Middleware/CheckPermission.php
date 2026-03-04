<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Core\User\Application\Services\AuthorizationServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(
        private readonly AuthorizationServiceInterface $authService
    ) {
    }

    /**
     * Usage: ->middleware('permission:leads.delete')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $activeScopeLevel = session('active_scope_level', 'SYSTEM');
        $activeScopeId = session('active_scope_id');

        $hasAccess = $this->authService->hasPermission($user->id, $permission, $activeScopeLevel, $activeScopeId);

        if (!$hasAccess) {
            if ($request->expectsJson() || $request->ajax()) {
                abort(403, 'Bạn không có quyền thực hiện hành động này trong phạm vi (scope) hiện tại.');
            }
            
            // Redirect based on active scope level
            $fallbackRoute = 'admin.dashboard'; // default valid scope for system
            
            return redirect()->route($fallbackRoute)->with('error', 'Bạn đã chuyển phạm vi hoạt động. Một số chức năng không còn khả dụng.');
        }

        return $next($request);
    }
}
