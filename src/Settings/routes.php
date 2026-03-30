<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Meridian\Settings\Presentation\Admin\SettingsController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('settings/{group}', [SettingsController::class, 'show'])->name('settings.show');
        Route::put('settings/{group}', [SettingsController::class, 'update'])->name('settings.update');
    });
