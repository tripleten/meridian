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

final readonly class UpdatePriceListCommand
{
    public function __construct(
        public string  $priceListId,
        public string  $name,
        public string  $code,
        public string  $currency_code,
        public ?string $channel_id,
        public ?string $customer_group_id,
        public bool    $is_default,
        public bool    $is_active,
    ) {}
}
