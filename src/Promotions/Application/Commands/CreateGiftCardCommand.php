<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Promotions\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Promotions\Application\Commands;

final class CreateGiftCardCommand
{
    public function __construct(
        public readonly ?string $code,
        public readonly int     $initial_balance,
        public readonly string  $currency_code,
        public readonly ?string $customer_id,
        public readonly ?string $expires_at,
    ) {}
}
