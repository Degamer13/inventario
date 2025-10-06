<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Users\UserIndex;
use App\Livewire\Role\RoleIndex;
use App\Livewire\Permission\PermissionIndex;
use App\Livewire\Responsables\ResponsableIndex;
use App\Livewire\Materiales\MaterialIndex;
use App\Livewire\Facilidades\FacilidadIndex;
use App\Livewire\MaquinariasFijas\MaquinariaFijaIndex;
use App\Livewire\Sistemas\SistemaIndex;
use App\Livewire\Vehiculos\VehiculoIndex;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Redirección para compatibilidad con Laravel Breeze/Jetstream
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Redirige /settings a /settings/profile
    Route::redirect('settings', 'settings/profile');

    // Rutas de usuarios (Livewire Volt)
    Route::get('users', UserIndex::class)
        ->middleware('can:list user') // permisos Spatie
        ->name('users.index');

    // Rutas de responsables (Livewire clásico)
    Route::get('responsables', ResponsableIndex::class)
        ->name('responsables.index'); // permiso se controla dentro del componente
  // Rutas de Materiales
    Route::get('materiales', MaterialIndex::class)
        ->name('materiales.index'); // permisos se controlan dentro del componente
  // Rutas de Facilidades
    Route::get('facilidades', FacilidadIndex::class)
        ->name('facilidades.index'); // permisos se controlan dentro del componente
    // Rutas de Maquinarias Fijas
    Route::get('maquinarias-fijas', MaquinariaFijaIndex::class)
        ->name('maquinarias-fijas.index'); // permisos se controlan dentro del componente
    // Rutas de Sistemas
    Route::get('sistemas', SistemaIndex::class)
        ->name('sistemas.index'); // permisos se controlan dentro del componente
    // Rutas de Vehículos
    Route::get('vehiculos', VehiculoIndex::class)
        ->name('vehiculos.index'); // permisos se controlan dentro del componente

    // Rutas de Roles (Livewire clásico)
    Route::get('roles', RoleIndex::class)
        ->name('roles.index')
        ->middleware('can:list role'); // control de permiso Spatie

    Route::get('permissions', PermissionIndex::class)
        ->name('permissions.index');

    // Rutas de configuración con Volt
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__ . '/auth.php';
