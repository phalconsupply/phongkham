<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectTenantUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return $next($request);
        }
        
        // Check if user is central admin (in whitelist)
        $centralAdminWhitelist = [
            'admin@phongkham.test',
        ];
        
        $isCentralAdmin = $user->hasRole('admin') && in_array($user->email, $centralAdminWhitelist);
        
        // If NOT central admin, redirect to tenant subdomain
        if (!$isCentralAdmin && !tenancy()->initialized) {
            // Extract tenant ID from email pattern (adminpk1@phongkham.test -> pk1)
            if (preg_match('/^admin(\w+)@/', $user->email, $matches)) {
                $tenantId = $matches[1];
                
                // Get tenant's primary domain
                $tenant = \App\Models\Tenant::find($tenantId);
                if ($tenant && $tenant->domains->isNotEmpty()) {
                    $tenantDomain = $tenant->domains->first()->domain;
                    $protocol = $request->secure() ? 'https' : 'http';
                    $requestUri = $request->getRequestUri();
                    
                    // Redirect to tenant domain with same path
                    return redirect()->away("{$protocol}://{$tenantDomain}{$requestUri}");
                }
            }
        }
        
        return $next($request);
    }
}
