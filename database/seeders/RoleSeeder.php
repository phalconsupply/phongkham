<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $doctor = Role::create(['name' => 'doctor']);
        $nurse = Role::create(['name' => 'nurse']);
        $receptionist = Role::create(['name' => 'receptionist']);

        // Create permissions (examples)
        $permissions = [
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
            'view encounters',
            'create encounters',
            'edit encounters',
            'view prescriptions',
            'create prescriptions',
            'edit prescriptions',
            'view appointments',
            'create appointments',
            'edit appointments',
            'delete appointments',
            'manage users',
            'manage tenants',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $doctor->givePermissionTo([
            'view patients', 'create patients', 'edit patients',
            'view encounters', 'create encounters', 'edit encounters',
            'view prescriptions', 'create prescriptions', 'edit prescriptions',
            'view appointments', 'create appointments', 'edit appointments',
        ]);
        $nurse->givePermissionTo([
            'view patients', 'create patients', 'edit patients',
            'view encounters', 'create encounters',
            'view appointments', 'create appointments', 'edit appointments',
        ]);
        $receptionist->givePermissionTo([
            'view patients', 'create patients', 'edit patients',
            'view appointments', 'create appointments', 'edit appointments', 'delete appointments',
        ]);
    }
}
