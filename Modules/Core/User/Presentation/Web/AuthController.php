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
            if ($user->default_center_id) {
                session(['current_center_id' => $user->default_center_id]);
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
        } else {
            $request->validate([
                'center_id' => 'required|uuid|exists:centers,id'
            ]);
            session(['current_center_id' => $centerId]);
        }

        return redirect()->back();
    }
}
