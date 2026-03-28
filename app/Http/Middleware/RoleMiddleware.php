<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware — Gate-checks the authenticated user's role.
 *
 * Usage in routes:
 *   ->middleware('role:admin')           // Admin only
 *   ->middleware('role:admin,manager')   // Admin OR Manager
 *   ->middleware('role:admin,manager,staff') // Any authenticated user (same as 'auth')
 *
 * Role hierarchy (descending power):
 *   admin > manager > staff
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Must be authenticated (failsafe — 'auth' middleware runs first in chain)
        if (! $user) {
            return redirect()->route('login');
        }

        // Check if user's role is in the allowed roles list
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Access denied. You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
