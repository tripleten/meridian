<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Database\Seeders
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Channels & currencies first — everything references them
            ChannelSeeder::class,
            CurrencySeeder::class,

            // 2. Roles and permissions — AdminUserSeeder depends on roles
            RolesAndPermissionsSeeder::class,

            // 3. Admin user
            AdminUserSeeder::class,

            // 4. Tax scaffolding — TaxRateSeeder depends on zones and classes
            TaxClassSeeder::class,
            TaxZoneSeeder::class,
            TaxRateSeeder::class,

            // 5. Store settings
            SettingsSeeder::class,

            // 6. Payment methods (disabled by default — configured in admin)
            PaymentMethodSeeder::class,
        ]);
    }
}
