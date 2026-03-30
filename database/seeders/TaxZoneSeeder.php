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

class TaxZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'code'      => 'uk',
                'name'      => 'United Kingdom',
                'countries' => json_encode(['GB']),
            ],
            [
                'code'      => 'eu',
                'name'      => 'European Union',
                'countries' => json_encode([
                    'AT','BE','BG','HR','CY','CZ','DK','EE','FI','FR',
                    'DE','GR','HU','IE','IT','LV','LT','LU','MT','NL',
                    'PL','PT','RO','SK','SI','ES','SE',
                ]),
            ],
            [
                'code'      => 'row',
                'name'      => 'Rest of World',
                'countries' => json_encode(['*']), // wildcard — matched last
            ],
        ];

        foreach ($zones as $zone) {
            DB::table('tax_zones')->insertOrIgnore(array_merge($zone, [
                'id'         => (string) Str::ulid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
