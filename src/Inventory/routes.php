<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Support\Facades\Route;
use Meridian\IdentityAccess\Presentation\Middleware\EnsureUserIsAdmin;
use Meridian\Inventory\Presentation\Admin\InventorySourceController;
use Meridian\Inventory\Presentation\Admin\StockController;

Route::middleware(['web', 'auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('inventory/sources', InventorySourceController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'inventory.sources.index',
                'create' => 'inventory.sources.create',
                'store'  => 'inventory.sources.store',
                'edit'   => 'inventory.sources.edit',
                'update' => 'inventory.sources.update',
            ]);

        Route::resource('inventory/stock', StockController::class)
            ->only(['index', 'edit', 'update'])
            ->names([
                'index'  => 'inventory.stock.index',
                'edit'   => 'inventory.stock.edit',
                'update' => 'inventory.stock.update',
            ]);
    });
