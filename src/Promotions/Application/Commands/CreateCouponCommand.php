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

final class CreateCouponCommand
{
    public function __construct(
        public readonly string  $code,
        public readonly ?string $description,
        public readonly string  $type,
        public readonly ?int    $usage_limit,
        public readonly ?int    $usage_limit_per_customer,
        public readonly ?string $cart_rule_id,
        public readonly bool    $is_active,
        public readonly ?string $valid_from,
        public readonly ?string $valid_until,
    ) {}
}
