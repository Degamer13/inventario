<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Users\UserIndex;
use App\Livewire\Role\RoleIndex;
use App\Livewire\Permission\PermissionIndex;
use App\Livewire\Responsables\ResponsableIndex;

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
