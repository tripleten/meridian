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

class TaxClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['code' => 'standard',    'name' => 'Standard Rate'],
            ['code' => 'reduced',     'name' => 'Reduced Rate'],
            ['code' => 'zero_rated',  'name' => 'Zero Rated'],
            ['code' => 'exempt',      'name' => 'Exempt'],
            ['code' => 'shipping',    'name' => 'Shipping'],
        ];

        foreach ($classes as $class) {
            DB::table('tax_classes')->insertOrIgnore(array_merge($class, [
                'id'         => (string) Str::ulid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
