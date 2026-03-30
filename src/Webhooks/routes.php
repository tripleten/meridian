<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Meridian\Webhooks\Presentation\Admin\WebhookController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('webhooks', WebhookController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
            ->names([
                'index'   => 'webhooks.index',
                'create'  => 'webhooks.create',
                'store'   => 'webhooks.store',
                'edit'    => 'webhooks.edit',
                'update'  => 'webhooks.update',
                'destroy' => 'webhooks.destroy',
            ]);

        Route::get('webhooks/{webhook}/deliveries', [WebhookController::class, 'deliveries'])
            ->name('webhooks.deliveries');
    });
