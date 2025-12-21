<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $isCentralAdmin = false;
        
        if ($user) {
            // Check if user is a TRUE central admin (matches whitelist)
            $centralAdminWhitelist = [
                'admin@phongkham.test',
            ];
            
            // Must have admin role AND be in whitelist AND NOT in tenant context
            $isCentralAdmin = $user->hasRole('admin') 
                && in_array($user->email, $centralAdminWhitelist)
                && !tenancy()->initialized;
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    ...$user->toArray(),
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                    'isCentralAdmin' => $isCentralAdmin,
                ] : null,
            ],
        ];
    }
}
