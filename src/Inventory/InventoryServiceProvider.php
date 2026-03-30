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

namespace Meridian\Inventory;

use Illuminate\Support\ServiceProvider;
use Meridian\Inventory\Domain\Repositories\InventoryItemRepositoryInterface;
use Meridian\Inventory\Domain\Repositories\InventorySourceRepositoryInterface;
use Meridian\Inventory\Infrastructure\Persistence\EloquentInventoryItemRepository;
use Meridian\Inventory\Infrastructure\Persistence\EloquentInventorySourceRepository;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InventorySourceRepositoryInterface::class, EloquentInventorySourceRepository::class);
        $this->app->bind(InventoryItemRepositoryInterface::class, EloquentInventoryItemRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
