<?php

namespace App\Http\Controllers\CentralAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Domain;
use App\Models\Tenant;
use App\Models\TenantTheme;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class TenantController extends Controller
{
    /**
     * Constructor - Ensure we're NEVER in tenant context
     * Middleware should prevent this, but double-check for security
     */
    public function __construct()
    {
        // Abort if somehow accessed from tenant context
        if (tenancy()->initialized) {
            abort(403, 'Central admin cannot be accessed from tenant domains.');
        }
    }

    public function index()
    {
        // Middleware already checks, but keep for extra security
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');
        
        return Inertia::render('CentralAdmin/Tenants/Index', [
            'tenants' => Tenant::with('domains')->get()
        ]);
    }

    public function create()
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');
        
        return Inertia::render('CentralAdmin/Tenants/Create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');
        $validated = $request->validate([
            'subdomain' => 'required|string|unique:domains,domain',
            'clinic_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email', // Will validate uniqueness in tenant context
            'admin_password' => 'required|string|min:8',
            'primary_color' => 'nullable|string',
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'id' => $validated['subdomain'],
        ]);

        $tenant->domains()->create([
            'domain' => $validated['subdomain'] . '.localhost',
        ]);

        // Store theme
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        TenantTheme::create([
            'id' => $tenant->id,
            'clinic_name' => $validated['clinic_name'],
            'logo_path' => $logoPath,
            'primary_color' => $validated['primary_color'] ?? '#3b82f6',
        ]);

        // Create admin user in tenant context
        $tenant->run(function () use ($validated) {
            $user = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
            ]);
            $user->assignRole('admin');
        });

        return redirect()->route('central.tenants.index')
            ->with('success', 'Tenant created successfully');
    }
}
