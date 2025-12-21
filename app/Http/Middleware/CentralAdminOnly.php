<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CentralAdminOnly
{
    /**
     * Handle an incoming request.
     * Only allow central database admin users (not tenant admins)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure we're NOT in a tenant context
        if (tenancy()->initialized) {
            abort(403, 'Tenant users cannot access central admin.');
        }

        // Ensure user is authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthenticated.');
        }

        $user = auth()->user();

        // Ensure user has admin role in CENTRAL database
        if (!$user->hasRole('admin')) {
            abort(403, 'Only central administrators can access this area.');
        }

        // CRITICAL: Check if this is a TRUE central admin user
        // Use WHITELIST of specific email addresses for central admin access
        // This is the most secure approach - explicitly define who can manage tenants
        $centralAdminWhitelist = [
            'admin@phongkham.test', // Main system administrator
            // Add more authorized central admin emails here as needed
        ];

        if (!in_array($user->email, $centralAdminWhitelist)) {
            abort(403, 'Access denied. Only authorized system administrators can access this area.');
        }

        return $next($request);
    }
}
