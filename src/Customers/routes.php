<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Support\Facades\Route;
use Meridian\Customers\Presentation\Admin\CustomerController;
use Meridian\Customers\Presentation\Admin\CustomerGroupController;
use Meridian\IdentityAccess\Presentation\Middleware\EnsureUserIsAdmin;

Route::middleware(['web', 'auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('customers', CustomerController::class)
            ->only(['index', 'show', 'edit', 'update']);

        Route::resource('customer-groups', CustomerGroupController::class)
            ->only(['index']);
    });
