<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Base Currency
    |--------------------------------------------------------------------------
    | The store's base/accounting currency. All prices are stored in this
    | currency as integer smallest units. Changing this after launch requires
    | a data migration — set it correctly from the start.
    */
    'base_currency' => env('MERIDIAN_BASE_CURRENCY', 'GBP'),

    /*
    |--------------------------------------------------------------------------
    | Outbox Event → Job Map
    |--------------------------------------------------------------------------
    | Maps fully-qualified domain event class names to the queue job that
    | should be dispatched when the outbox relay processes that event.
    | Add an entry here for every domain event that has side effects.
    */
    'outbox' => [
        'event_job_map' => [
            // Example (uncomment and extend as events are implemented):
            // \Meridian\Orders\Domain\Events\OrderPlaced::class
            //     => \Meridian\Orders\Infrastructure\Jobs\ProcessOrderPlacedJob::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate API
    |--------------------------------------------------------------------------
    | Used by UpdateExchangeRatesJob to fetch live rates.
    | Supported drivers: 'ecb' (European Central Bank, free), 'openexchangerates'
    */
    'exchange_rates' => [
        'driver'  => env('EXCHANGE_RATE_DRIVER', 'ecb'),
        'api_key' => env('EXCHANGE_RATE_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | VAT / Tax
    |--------------------------------------------------------------------------
    */
    'tax' => [
        'vies_wsdl'    => 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl',
        'hmrc_api_url' => 'https://api.service.hmrc.gov.uk/organisations/vat/check-vat-number',
    ],

];
