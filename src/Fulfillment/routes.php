<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Meridian\Fulfillment\Presentation\Admin\ShipmentController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('orders/{order}/shipments', ShipmentController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'orders.shipments.index',
                'create' => 'orders.shipments.create',
                'store'  => 'orders.shipments.store',
                'edit'   => 'orders.shipments.edit',
                'update' => 'orders.shipments.update',
            ]);
    });
