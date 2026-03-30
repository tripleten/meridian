<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Tax
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Support\Facades\Route;
use Meridian\IdentityAccess\Presentation\Middleware\EnsureUserIsAdmin;
use Meridian\Tax\Presentation\Admin\TaxClassController;
use Meridian\Tax\Presentation\Admin\TaxRateController;
use Meridian\Tax\Presentation\Admin\TaxRuleController;
use Meridian\Tax\Presentation\Admin\TaxZoneController;

Route::middleware(['web', 'auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        Route::resource('tax/classes', TaxClassController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'tax.classes.index',
                'create' => 'tax.classes.create',
                'store'  => 'tax.classes.store',
                'edit'   => 'tax.classes.edit',
                'update' => 'tax.classes.update',
            ]);

        Route::resource('tax/zones', TaxZoneController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'tax.zones.index',
                'create' => 'tax.zones.create',
                'store'  => 'tax.zones.store',
                'edit'   => 'tax.zones.edit',
                'update' => 'tax.zones.update',
            ]);

        Route::resource('tax/rates', TaxRateController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'tax.rates.index',
                'create' => 'tax.rates.create',
                'store'  => 'tax.rates.store',
                'edit'   => 'tax.rates.edit',
                'update' => 'tax.rates.update',
            ]);

        Route::resource('tax/rules', TaxRuleController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'tax.rules.index',
                'create' => 'tax.rules.create',
                'store'  => 'tax.rules.store',
                'edit'   => 'tax.rules.edit',
                'update' => 'tax.rules.update',
            ]);
    });
