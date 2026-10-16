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
Route::middleware(['auth', 'verified'])->name('crud.')->group(function () {
    foreach (Modules::slugs() as $slug) {
        Volt::route($slug, "{$slug}.index")->name($slug);
    }
});

require __DIR__.'/auth.php';
