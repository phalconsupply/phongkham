<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CentralAdmin\TenantController;
use App\Http\Controllers\CentralAdmin\RolePermissionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', \App\Http\Middleware\RedirectTenantUsers::class])->name('dashboard');

Route::middleware(['auth', \App\Http\Middleware\RedirectTenantUsers::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Central Admin Routes - ONLY for central admin, NOT accessible from tenant domains
Route::middleware([
    'auth',
    \App\Http\Middleware\PreventAccessFromTenantDomains::class,
    \App\Http\Middleware\CentralAdminOnly::class,
])->prefix('central')->name('central.')->group(function () {
    
    // Role & Permission Management
    Route::get('/role-permissions', [RolePermissionController::class, 'index'])->name('role-permissions.index');
    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.permissions.update');
    Route::post('/permissions', [RolePermissionController::class, 'createPermission'])->name('permissions.create');
    Route::delete('/permissions/{permission}', [RolePermissionController::class, 'deletePermission'])->name('permissions.delete');
    Route::post('/roles', [RolePermissionController::class, 'createRole'])->name('roles.create');
    Route::delete('/roles/{role}', [RolePermissionController::class, 'deleteRole'])->name('roles.delete');
    Route::resource('tenants', TenantController::class);
});

require __DIR__.'/auth.php';
