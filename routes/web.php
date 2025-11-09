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
use App\Livewire\Salidas\SalidaIndex; // Componente de Salidas

// [ ... Rutas Públicas y Redirecciones ... ]
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

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

    // Rutas de responsables
    Route::get('responsables', ResponsableIndex::class)
        ->name('responsables.index');

    // Rutas de Inventario
    Route::get('materiales', MaterialIndex::class)
        ->name('materiales.index');

    Route::get('facilidades', FacilidadIndex::class)
        ->name('facilidades.index');

    Route::get('maquinarias-fijas', MaquinariaFijaIndex::class)
        ->name('maquinarias-fijas.index');

    Route::get('sistemas', SistemaIndex::class)
        ->name('sistemas.index');

    Route::get('vehiculos', VehiculoIndex::class)
        ->name('vehiculos.index');

    // Rutas de SALIDAS (VERIFICADA LA SINTAXIS)
    Route::get('salidas', SalidaIndex::class)
        ->name('salidas.index');

    // Rutas de Roles y Permisos
    Route::get('roles', RoleIndex::class)
        ->name('roles.index')
        ->middleware('can:list role');

    Route::get('permissions', PermissionIndex::class)
        ->name('permissions.index');

    // Rutas de configuración con Volt
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__ . '/auth.php';
