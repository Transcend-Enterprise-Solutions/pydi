<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$role)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (in_array($user->user_role, $role)) {
                return $next($request);
            }

            if($user->active_status == 1) {
                if ($user->user_role === 'user') {
                    return redirect()->route('data-entry');
                } else {
                    return redirect()->route('dashboard');
                }
            }else{
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                // Return to login with appropriate message based on status
                switch($user->active_status) {
                    case 0:
                        return redirect()->route('login')
                            ->withErrors(['login' => 'Your account is pending approval. Please wait for admin verification.']);
                    case 2:
                        return redirect()->route('login')
                            ->withErrors(['login' => 'Your account has been deactivated. Please contact the administrator.']);
                    default:
                        return redirect()->route('login')
                            ->withErrors(['login' => 'Account status is invalid. Please contact the administrator.']);
                }
            }

        }

        return redirect('/login');
    }
}
