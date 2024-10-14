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

            if ($user->user_role === 'homeowner') {
                return redirect()->route('home');
            } else {
                return redirect()->route('dashboard');
            }

        }

        return redirect('/login');
    }
}
