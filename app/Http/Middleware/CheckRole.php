<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            Log::info('User is not logged in. Required roles: ' . implode(', ', $roles));
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            Log::info('Unauthorized access attempt. User role: ' . $user->role . ', Required roles: ' . implode(', ', $roles));
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
