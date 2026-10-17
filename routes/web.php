<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/login', 'welcome')->name('login');
Route::inertia('/{any?}', 'welcome')->where('any', '.*')->name('home');
