<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Reporting
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Support\Facades\Route;
use Meridian\IdentityAccess\Presentation\Middleware\EnsureUserIsAdmin;
use Meridian\Reporting\Presentation\Admin\DashboardController;
use Meridian\Reporting\Presentation\Admin\ReportsController;

Route::middleware(['web', 'auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('home');
        Route::get('reports', ReportsController::class)->name('reports.index');
    });
