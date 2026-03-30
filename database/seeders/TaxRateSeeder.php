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

class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        $ukZoneId  = DB::table('tax_zones')->where('code', 'uk')->value('id');
        $euZoneId  = DB::table('tax_zones')->where('code', 'eu')->value('id');
        $rowZoneId = DB::table('tax_zones')->where('code', 'row')->value('id');

        $rates = [
            // UK rates
            ['tax_zone_id' => $ukZoneId,  'code' => 'uk_standard',     'rate' => 0.2000, 'name' => 'UK Standard VAT 20%',  'is_shipping_taxable' => true],
            ['tax_zone_id' => $ukZoneId,  'code' => 'uk_reduced',      'rate' => 0.0500, 'name' => 'UK Reduced VAT 5%',    'is_shipping_taxable' => false],
            ['tax_zone_id' => $ukZoneId,  'code' => 'uk_zero',         'rate' => 0.0000, 'name' => 'UK Zero Rate 0%',      'is_shipping_taxable' => false],

            // EU rates
            ['tax_zone_id' => $euZoneId,  'code' => 'eu_standard',     'rate' => 0.2000, 'name' => 'EU Standard VAT 20%',  'is_shipping_taxable' => true],
            ['tax_zone_id' => $euZoneId,  'code' => 'eu_reduced',      'rate' => 0.0500, 'name' => 'EU Reduced VAT 5%',    'is_shipping_taxable' => false],
            ['tax_zone_id' => $euZoneId,  'code' => 'eu_zero',         'rate' => 0.0000, 'name' => 'EU Zero Rate 0%',      'is_shipping_taxable' => false],

            // Rest of World
            ['tax_zone_id' => $rowZoneId, 'code' => 'row_no_tax',      'rate' => 0.0000, 'name' => 'ROW No Tax 0%',        'is_shipping_taxable' => false],
        ];

        foreach ($rates as $rate) {
            if ($rate['tax_zone_id'] === null) {
                continue;
            }

            DB::table('tax_rates')->insertOrIgnore(array_merge($rate, [
                'id'                  => (string) Str::ulid(),
                'type'                => 'inclusive',
                'is_compound'         => false,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]));
        }
    }
}
