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

namespace Meridian\Customers;

use Illuminate\Support\ServiceProvider;
use Meridian\Customers\Domain\Repositories\CustomerGroupRepositoryInterface;
use Meridian\Customers\Domain\Repositories\CustomerRepositoryInterface;
use Meridian\Customers\Infrastructure\Persistence\EloquentCustomerGroupRepository;
use Meridian\Customers\Infrastructure\Persistence\EloquentCustomerRepository;

final class CustomersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
        $this->app->bind(CustomerGroupRepositoryInterface::class, EloquentCustomerGroupRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
