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

namespace Meridian\Pricing;

use Illuminate\Support\ServiceProvider;
use Meridian\Pricing\Domain\Repositories\PriceListRepositoryInterface;
use Meridian\Pricing\Infrastructure\Persistence\EloquentPriceListRepository;

class PricingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PriceListRepositoryInterface::class, EloquentPriceListRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
