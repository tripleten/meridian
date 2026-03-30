<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Support\Facades\Route;
use Meridian\IdentityAccess\Presentation\Middleware\EnsureUserIsAdmin;
use Meridian\Pricing\Presentation\Admin\PriceListController;

Route::middleware(['web', 'auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('pricing/price-lists', PriceListController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
            ->names([
                'index'   => 'pricing.price-lists.index',
                'create'  => 'pricing.price-lists.create',
                'store'   => 'pricing.price-lists.store',
                'edit'    => 'pricing.price-lists.edit',
                'update'  => 'pricing.price-lists.update',
                'destroy' => 'pricing.price-lists.destroy',
            ]);
    });
