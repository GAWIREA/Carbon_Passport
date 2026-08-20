<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Accepts one or more role slugs (e.g. 'admin', 'user,seller').
     * Redirects unauthenticated visitors to /login.
     * Redirects authenticated users with an incorrect role to their own dashboard.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role->value;

        if (! in_array($userRole, $roles, strict: true)) {
            return redirect()->route($request->user()->role->dashboardRoute());
        }

        return $next($request);
    }
}
