<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (!in_array($user->role, $roles)) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}
