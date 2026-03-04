<?php

declare(strict_types=1);

namespace Modules\Core\User\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('user::auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            $authService = app(\Modules\Core\User\Application\Services\AuthorizationServiceInterface::class);
            $hasGlobalScope = $authService->hasGlobalScope($user->id);

            if ($hasGlobalScope) {
                // Global scope takes precedence on fresh login
                session(['active_scope_level' => 'SYSTEM', 'active_scope_id' => null]);
                session()->forget('current_center_id');
            } elseif ($user->default_center_id) {
                session(['current_center_id' => $user->default_center_id]);
                session(['active_scope_level' => 'CENTER', 'active_scope_id' => $user->default_center_id]);
            }

            // Audit Log: Login using SYSTEM_OWNER
            $systemOwnerRole = \Illuminate\Support\Facades\DB::table('roles')->where('name', 'SYSTEM_OWNER')->value('id');
            if ($systemOwnerRole) {
                $hasSystemOwner = \Illuminate\Support\Facades\DB::table('user_roles')
                    ->where('user_id', $user->id)
                    ->where('role_id', $systemOwnerRole)
                    ->exists();
                if ($hasSystemOwner) {
                    \Modules\Core\User\Infrastructure\Services\SystemAuditLogger::log('LOGIN_SYSTEM_OWNER', $user->id, $user->id);
                }
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function switchCenter(Request $request)
    {
        $centerId = $request->input('center_id');

        if (empty($centerId)) {
            // Clear center context — only super admins should reach this
            session()->forget('current_center_id');
            session(['active_scope_level' => 'SYSTEM', 'active_scope_id' => null]);
        } else {
            $request->validate([
                'center_id' => 'required|uuid|exists:centers,id'
            ]);
            session(['current_center_id' => $centerId]);
            session(['active_scope_level' => 'CENTER', 'active_scope_id' => $centerId]);
        }

        return redirect()->back();
    }
}
