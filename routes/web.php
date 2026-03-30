<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    // Admin panel entry — redirect /admin to users list until a dashboard page is built
    Route::redirect('admin', '/admin/users')->name('admin.home');
});

require __DIR__.'/settings.php';
