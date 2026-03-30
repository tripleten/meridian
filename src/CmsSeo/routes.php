<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Support\Facades\Route;
use Meridian\CmsSeo\Presentation\Admin\CmsBlockController;
use Meridian\CmsSeo\Presentation\Admin\CmsPageController;
use Meridian\IdentityAccess\Presentation\Middleware\EnsureUserIsAdmin;

Route::middleware(['web', 'auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('cms-pages', CmsPageController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('cms-blocks', CmsBlockController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });
