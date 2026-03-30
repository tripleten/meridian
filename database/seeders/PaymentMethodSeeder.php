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

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'code'       => 'stripe',
                'name'       => 'Credit / Debit Card (Stripe)',
                'is_enabled' => false,
                'sort_order' => 10,
                'config'     => json_encode([
                    'publishable_key' => '',
                    'secret_key'      => '',
                    'webhook_secret'  => '',
                    'capture_method'  => 'automatic', // 'automatic' | 'manual'
                ]),
            ],
            [
                'code'       => 'paypal',
                'name'       => 'PayPal',
                'is_enabled' => false,
                'sort_order' => 20,
                'config'     => json_encode([
                    'client_id'     => '',
                    'client_secret' => '',
                    'mode'          => 'sandbox', // 'sandbox' | 'live'
                ]),
            ],
            [
                'code'       => 'bank_transfer',
                'name'       => 'Bank Transfer',
                'is_enabled' => false,
                'sort_order' => 30,
                'config'     => json_encode([
                    'account_name'   => '',
                    'account_number' => '',
                    'sort_code'      => '',
                    'iban'           => '',
                    'bic'            => '',
                    'instructions'   => 'Please use your order number as payment reference.',
                ]),
            ],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->insertOrIgnore(array_merge($method, [
                'id'         => (string) Str::ulid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
