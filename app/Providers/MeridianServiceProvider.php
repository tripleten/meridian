<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    App\Providers
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Meridian\Shared\Infrastructure\Outbox\OutboxRelay;
use Meridian\Shared\Infrastructure\Outbox\OutboxWriter;

/**
 * Root service provider for the Meridian platform.
 *
 * Binds shared infrastructure and bootstraps all bounded-context
 * service providers. Add new module providers to the $modules array
 * as each bounded context is implemented.
 */
final class MeridianServiceProvider extends ServiceProvider
{
    /**
     * Module service providers to register.
     * Add a new entry here when scaffolding a new bounded context.
     *
     * @var array<class-string<ServiceProvider>>
     */
    private array $modules = [
        \Meridian\IdentityAccess\IdentityAccessServiceProvider::class,
        \Meridian\Catalog\CatalogServiceProvider::class,
        \Meridian\Customers\CustomersServiceProvider::class,
        \Meridian\CmsSeo\CmsSeoServiceProvider::class,
        \Meridian\Inventory\InventoryServiceProvider::class,
        \Meridian\Pricing\PricingServiceProvider::class,
        \Meridian\Orders\OrdersServiceProvider::class,
        \Meridian\Payments\PaymentsServiceProvider::class,
        \Meridian\Fulfillment\FulfillmentServiceProvider::class,
    ];

    public function register(): void
    {
        $this->registerSharedInfrastructure();
        $this->registerModules();
    }

    public function boot(): void
    {
        //
    }

    private function registerSharedInfrastructure(): void
    {
        // Outbox
        $this->app->singleton(OutboxWriter::class);
        $this->app->singleton(OutboxRelay::class, fn () => new OutboxRelay(
            eventJobMap: config('meridian.outbox.event_job_map', []),
        ));
    }

    private function registerModules(): void
    {
        foreach ($this->modules as $provider) {
            $this->app->register($provider);
        }
    }
}
