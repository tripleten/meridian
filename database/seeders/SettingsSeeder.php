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

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // --- Store identity ---
            ['group' => 'general', 'key' => 'store_name',          'value' => 'My Store',          'type' => 'string'],
            ['group' => 'general', 'key' => 'store_email',         'value' => 'hello@example.com', 'type' => 'string'],
            ['group' => 'general', 'key' => 'store_phone',         'value' => '',                  'type' => 'string'],
            ['group' => 'general', 'key' => 'store_address',       'value' => '{}',                'type' => 'json'],
            ['group' => 'general', 'key' => 'vat_number',          'value' => '',                  'type' => 'string'],

            // --- Locale / currency ---
            ['group' => 'locale',  'key' => 'default_locale',      'value' => 'en_GB',             'type' => 'string'],
            ['group' => 'locale',  'key' => 'timezone',            'value' => 'Europe/London',     'type' => 'string'],
            ['group' => 'locale',  'key' => 'date_format',         'value' => 'd/m/Y',             'type' => 'string'],

            // --- SEO ---
            ['group' => 'seo',     'key' => 'meta_title_suffix',   'value' => '',                  'type' => 'string'],
            ['group' => 'seo',     'key' => 'meta_description',    'value' => '',                  'type' => 'string'],
            ['group' => 'seo',     'key' => 'robots_txt',          'value' => "User-agent: *\nAllow: /", 'type' => 'string'],

            // --- Analytics / scripts ---
            ['group' => 'scripts', 'key' => 'gtm_id',              'value' => '',                  'type' => 'string'],
            ['group' => 'scripts', 'key' => 'header_scripts',      'value' => '',                  'type' => 'text'],
            ['group' => 'scripts', 'key' => 'footer_scripts',      'value' => '',                  'type' => 'text'],

            // --- Social sharing ---
            ['group' => 'social',  'key' => 'og_image_url',        'value' => '',                  'type' => 'string'],
            ['group' => 'social',  'key' => 'twitter_handle',      'value' => '',                  'type' => 'string'],
            ['group' => 'social',  'key' => 'facebook_page_id',    'value' => '',                  'type' => 'string'],

            // --- GDPR / Cookie consent ---
            ['group' => 'gdpr',    'key' => 'cookie_policy_enabled', 'value' => 'true',            'type' => 'boolean'],
            ['group' => 'gdpr',    'key' => 'cookie_policy_version', 'value' => '1.0',             'type' => 'string'],
            ['group' => 'gdpr',    'key' => 'privacy_policy_url',    'value' => '/privacy-policy', 'type' => 'string'],
            ['group' => 'gdpr',    'key' => 'cookie_policy_url',     'value' => '/cookie-policy',  'type' => 'string'],

            // --- Order settings ---
            ['group' => 'orders',  'key' => 'guest_checkout',      'value' => 'true',              'type' => 'boolean'],
            ['group' => 'orders',  'key' => 'order_number_prefix',  'value' => 'ORD-',             'type' => 'string'],
            ['group' => 'orders',  'key' => 'invoice_number_prefix', 'value' => 'INV-',            'type' => 'string'],
            ['group' => 'orders',  'key' => 'credit_memo_prefix',    'value' => 'CM-',             'type' => 'string'],

            // --- Email ---
            ['group' => 'email',   'key' => 'from_name',           'value' => 'My Store',          'type' => 'string'],
            ['group' => 'email',   'key' => 'from_address',        'value' => 'no-reply@example.com', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            $exists = DB::table('settings')
                ->where('group', $setting['group'])
                ->where('key', $setting['key'])
                ->exists();

            if (! $exists) {
                DB::table('settings')->insert(array_merge($setting, [
                    'id'         => (string) Str::ulid(),
                    'is_public'  => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
