<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Meridian\Orders\Presentation\Admin\OrderController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::post('orders/{order}/comments', [OrderController::class, 'addComment'])->name('orders.comments.store');
    });
