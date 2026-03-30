<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Meridian\Payments\Presentation\Admin\PaymentMethodController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('payments/methods', PaymentMethodController::class)
            ->only(['index', 'edit', 'update'])
            ->names([
                'index'  => 'payments.methods.index',
                'edit'   => 'payments.methods.edit',
                'update' => 'payments.methods.update',
            ]);
    });
