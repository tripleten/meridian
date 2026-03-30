<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Support\Facades\Route;
use Meridian\IdentityAccess\Presentation\Admin\AdminUserController;
use Meridian\IdentityAccess\Presentation\Middleware\EnsureUserIsAdmin;

Route::middleware(['web', 'auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        // Admin user management
        // EnsureUserIsAdmin (on the group) already gates all admin routes.
        // Granular permission checks happen inside the controller/handler.
        Route::resource('users', AdminUserController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });
