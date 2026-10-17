<?php

use App\Support\Modules;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// CRUD de cada modelo: /pets -> resources/views/livewire/pets/index.blade.php (ruta "crud.pets")
// Cada modulo exige su permiso; Livewire reaplica el middleware "can" en cada accion del componente.
Route::middleware(['auth', 'verified'])->name('crud.')->group(function () {
    foreach (Modules::slugs() as $slug) {
        Volt::route($slug, "{$slug}.index")->name($slug)->middleware('can:'.Modules::permission($slug));
    }
});

// Area del adoptante: catalogo de mascotas disponibles, solicitudes de adopcion y sus datos personales.
Route::middleware(['auth', 'verified', 'can:adopt'])->name('adopter.')->group(function () {
    Volt::route('adoptar', 'adopter.pets')->name('pets');
    Volt::route('adoptar/{pet}', 'adopter.pet')->name('pet');
    Volt::route('mis-solicitudes', 'adopter.requests')->name('requests');
    Volt::route('mi-perfil-adoptante', 'adopter.profile')->name('profile');
});

require __DIR__.'/auth.php';
