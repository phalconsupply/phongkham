<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions grouped by module
        $permissions = [
            // Patient Module
            'patient.view' => 'View patients',
            'patient.create' => 'Create new patients',
            'patient.edit' => 'Edit patient information',
            'patient.delete' => 'Delete patients',
            
            // Encounter Module
            'encounter.view' => 'View encounters',
            'encounter.create' => 'Create new encounters',
            'encounter.edit' => 'Edit encounters',
            'encounter.delete' => 'Delete encounters',
            
            // Prescription Module
            'prescription.view' => 'View prescriptions',
            'prescription.create' => 'Create prescriptions',
            'prescription.edit' => 'Edit prescriptions',
            'prescription.delete' => 'Delete prescriptions',
            
            // Appointment Module (future)
            'appointment.view' => 'View appointments',
            'appointment.create' => 'Create appointments',
            'appointment.edit' => 'Edit appointments',
            'appointment.delete' => 'Delete appointments',
            
            // Tenant Management (Central only)
            'tenant.view' => 'View tenants',
            'tenant.create' => 'Create tenants',
            'tenant.edit' => 'Edit tenants',
            'tenant.delete' => 'Delete tenants',
            
            // Role & Permission Management (Central only)
            'role.view' => 'View roles',
            'role.manage' => 'Manage roles and permissions',
            
            // Reports
            'report.view' => 'View reports',
            'report.export' => 'Export reports',
        ];

        // Create all permissions
        foreach ($permissions as $name => $description) {
            Permission::create([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Assign permissions to default roles
     */
    private function assignPermissionsToRoles(): void
    {
        // Admin - Full access
        $admin = Role::findByName('admin');
        $admin->givePermissionTo(Permission::all());

        // Doctor - Patient care and medical records
        $doctor = Role::findByName('doctor');
        $doctor->givePermissionTo([
            'patient.view',
            'patient.create',
            'patient.edit',
            'encounter.view',
            'encounter.create',
            'encounter.edit',
            'prescription.view',
            'prescription.create',
            'prescription.edit',
            'appointment.view',
            'appointment.create',
            'appointment.edit',
            'report.view',
        ]);

        // Nurse - Patient care support
        $nurse = Role::findByName('nurse');
        $nurse->givePermissionTo([
            'patient.view',
            'patient.edit',
            'encounter.view',
            'encounter.create',
            'prescription.view',
            'appointment.view',
            'appointment.create',
            'appointment.edit',
        ]);

        // Receptionist - Front desk operations
        $receptionist = Role::findByName('receptionist');
        $receptionist->givePermissionTo([
            'patient.view',
            'patient.create',
            'patient.edit',
            'appointment.view',
            'appointment.create',
            'appointment.edit',
            'appointment.delete',
        ]);
    }
}
