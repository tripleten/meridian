<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Shared\Infrastructure\Outbox
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Shared\Infrastructure\Outbox;

/**
 * Request-scoped currency context.
 *
 * Set by CurrencyMiddleware from the user's cookie/session or geo-IP default.
 * Injected into PriceFormatter and checkout services for display-currency conversion.
 * Never a static global — injectable only.
 */
final readonly class CurrencyContext
{
    public function __construct(
        public readonly string $code,         // ISO 4217 e.g. 'GBP', 'EUR'
        public readonly float  $exchangeRate, // relative to base currency
        public readonly string $symbol,       // e.g. '£', '€'
        public readonly string $symbolPosition, // 'before' | 'after'
        public readonly int    $decimalPlaces,
    ) {}

    public function isBaseCurrency(): bool
    {
        return $this->exchangeRate === 1.0;
    }
}
