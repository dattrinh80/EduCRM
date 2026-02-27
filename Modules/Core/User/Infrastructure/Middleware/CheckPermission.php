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

        if (!$this->authService->can($user->id, $permission)) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        return $next($request);
    }
}
