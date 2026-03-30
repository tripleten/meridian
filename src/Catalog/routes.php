<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Support\Facades\Route;
use Meridian\Catalog\Presentation\Admin\Brands\BrandController;
use Meridian\Catalog\Presentation\Admin\Categories\CategoryController;
use Meridian\Catalog\Presentation\Admin\Products\ProductController;
use Meridian\IdentityAccess\Presentation\Middleware\EnsureUserIsAdmin;

Route::middleware(['web', 'auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        Route::resource('products', ProductController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('categories', CategoryController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('brands', BrandController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });
