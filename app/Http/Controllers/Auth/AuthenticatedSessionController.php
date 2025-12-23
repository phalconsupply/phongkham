<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        
        // Check if user is central admin (in whitelist)
        $centralAdminWhitelist = [
            'admin@phongkham.test',
        ];
        
        $isCentralAdmin = $user->hasRole('admin') && in_array($user->email, $centralAdminWhitelist);
        
        // If NOT central admin, redirect to tenant subdomain
        if (!$isCentralAdmin) {
            // Get user's tenant from their email or a tenant_id field
            // For now, extract tenant ID from email pattern (adminpk1@phongkham.test -> pk1)
            if (preg_match('/^admin(\w+)@/', $user->email, $matches)) {
                $tenantId = $matches[1];
                
                // Get tenant's primary domain
                $tenant = \App\Models\Tenant::find($tenantId);
                if ($tenant && $tenant->domains->isNotEmpty()) {
                    $tenantDomain = $tenant->domains->first()->domain;
                    $protocol = $request->secure() ? 'https' : 'http';
                    $intendedUrl = session()->pull('url.intended');
                    
                    // Redirect to tenant domain
                    return redirect()->away("{$protocol}://{$tenantDomain}/dashboard");
                }
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
