<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Patient\Controllers\PatientController;
use App\Modules\Encounter\Controllers\EncounterController;
use App\Modules\Prescription\Controllers\PrescriptionController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::get('/', function () {
    return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
});

// Module Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->name('dashboard');
    
    Route::resource('patients', PatientController::class);
    Route::resource('encounters', EncounterController::class);
    Route::resource('prescriptions', PrescriptionController::class);
});

require __DIR__.'/auth.php';
