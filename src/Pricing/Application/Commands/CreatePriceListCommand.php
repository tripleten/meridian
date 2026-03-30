<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Application\Commands;

final readonly class CreatePriceListCommand
{
    public function __construct(
        public string  $name,
        public string  $code,
        public string  $currency_code    = 'GBP',
        public ?string $channel_id       = null,
        public ?string $customer_group_id = null,
        public bool    $is_default       = false,
        public bool    $is_active        = true,
    ) {}
}
