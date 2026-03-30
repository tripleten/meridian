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

namespace Meridian\Catalog;

use Illuminate\Support\ServiceProvider;
use Meridian\Catalog\Domain\Repositories\BrandRepositoryInterface;
use Meridian\Catalog\Domain\Repositories\CategoryRepositoryInterface;
use Meridian\Catalog\Domain\Repositories\ProductRepositoryInterface;
use Meridian\Catalog\Infrastructure\Persistence\EloquentBrandRepository;
use Meridian\Catalog\Infrastructure\Persistence\EloquentCategoryRepository;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProductRepository;

final class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, EloquentBrandRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
