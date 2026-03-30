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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChannelSeeder extends Seeder
{
    public function run(): void
    {
        $id = (string) Str::ulid();

        DB::table('channels')->insertOrIgnore([
            'id'                  => $id,
            'code'                => 'default',
            'name'                => 'Main Store',
            'default_locale'      => 'en_GB',
            'supported_locales'   => json_encode(['en_GB']),
            'default_currency'    => 'GBP',
            'supported_currencies' => json_encode(['GBP', 'EUR', 'USD']),
            'domain'              => config('app.url', 'http://localhost'),
            'is_active'           => true,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }
}
