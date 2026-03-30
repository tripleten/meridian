<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Meridian\Promotions\Presentation\Admin\CartRuleController;
use Meridian\Promotions\Presentation\Admin\CatalogPriceRuleController;
use Meridian\Promotions\Presentation\Admin\CouponController;
use Meridian\Promotions\Presentation\Admin\GiftCardController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('promotions/coupons', CouponController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'promotions.coupons.index',
                'create' => 'promotions.coupons.create',
                'store'  => 'promotions.coupons.store',
                'edit'   => 'promotions.coupons.edit',
                'update' => 'promotions.coupons.update',
            ]);

        Route::resource('promotions/cart-rules', CartRuleController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'promotions.cart-rules.index',
                'create' => 'promotions.cart-rules.create',
                'store'  => 'promotions.cart-rules.store',
                'edit'   => 'promotions.cart-rules.edit',
                'update' => 'promotions.cart-rules.update',
            ]);

        Route::resource('promotions/catalog-rules', CatalogPriceRuleController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names([
                'index'  => 'promotions.catalog-rules.index',
                'create' => 'promotions.catalog-rules.create',
                'store'  => 'promotions.catalog-rules.store',
                'edit'   => 'promotions.catalog-rules.edit',
                'update' => 'promotions.catalog-rules.update',
            ]);

        Route::resource('promotions/gift-cards', GiftCardController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
            ->names([
                'index'  => 'promotions.gift-cards.index',
                'create' => 'promotions.gift-cards.create',
                'store'  => 'promotions.gift-cards.store',
                'show'   => 'promotions.gift-cards.show',
                'edit'   => 'promotions.gift-cards.edit',
                'update' => 'promotions.gift-cards.update',
            ]);
    });
