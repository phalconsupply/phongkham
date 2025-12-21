<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAccessFromTenantDomains
{
    /**
     * Handle an incoming request.
     * Prevent tenant domains from accessing central admin routes
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if tenancy has been initialized (means we're on a tenant domain)
        if (tenancy()->initialized) {
            abort(403, 'Access to central admin from tenant domains is forbidden.');
        }

        return $next($request);
    }
}
