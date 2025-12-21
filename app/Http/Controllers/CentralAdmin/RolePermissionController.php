<?php

namespace App\Http\Controllers\CentralAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class RolePermissionController extends Controller
{
    /**
     * Constructor - Ensure we're NEVER in tenant context
     */
    public function __construct()
    {
        if (tenancy()->initialized) {
            abort(403, 'Role management cannot be accessed from tenant domains.');
        }
    }

    /**
     * Display role and permission management
     */
    public function index()
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            // Group permissions by module (e.g., "patient.view" -> "patient")
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'other';
        });

        return Inertia::render('CentralAdmin/RolePermissions/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request, $roleId)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::findOrFail($roleId);
        
        // Sync permissions for this role
        $role->syncPermissions($validated['permissions']);

        return redirect()->back()->with('success', "Permissions updated for role: {$role->name}");
    }

    /**
     * Create new permission
     */
    public function createPermission(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');

        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'guard_name' => 'nullable|string',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
        ]);

        return redirect()->back()->with('success', 'Permission created successfully');
    }

    /**
     * Delete permission
     */
    public function deletePermission($permissionId)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');

        $permission = Permission::findOrFail($permissionId);
        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully');
    }

    /**
     * Create new role
     */
    public function createRole(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'nullable|string',
        ]);

        Role::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
        ]);

        return redirect()->back()->with('success', 'Role created successfully');
    }

    /**
     * Delete role
     */
    public function deleteRole($roleId)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');

        $role = Role::findOrFail($roleId);
        
        // Prevent deleting core roles
        $protectedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (in_array($role->name, $protectedRoles)) {
            return redirect()->back()->with('error', 'Cannot delete core system roles');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully');
    }
}
