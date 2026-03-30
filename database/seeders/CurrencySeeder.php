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

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'code'            => 'GBP',
                'name'            => 'British Pound Sterling',
                'symbol'          => '£',
                'symbol_position' => 'before',
                'decimal_places'  => 2,
                'exchange_rate'   => 1.000000,
                'is_base'         => true,
                'is_active'       => true,
            ],
            [
                'code'            => 'EUR',
                'name'            => 'Euro',
                'symbol'          => '€',
                'symbol_position' => 'before',
                'decimal_places'  => 2,
                'exchange_rate'   => 1.170000, // placeholder — updated by UpdateExchangeRatesJob
                'is_base'         => false,
                'is_active'       => true,
            ],
            [
                'code'            => 'USD',
                'name'            => 'United States Dollar',
                'symbol'          => '$',
                'symbol_position' => 'before',
                'decimal_places'  => 2,
                'exchange_rate'   => 1.280000, // placeholder
                'is_base'         => false,
                'is_active'       => true,
            ],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->insertOrIgnore(array_merge($currency, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
