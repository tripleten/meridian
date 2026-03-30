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

use Meridian\Promotions\Infrastructure\Persistence\EloquentCoupon;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class CreateCouponHandler
{
    public function handle(CreateCouponCommand $command): EloquentCoupon
    {
        if (EloquentCoupon::where('code', $command->code)->exists()) {
            throw new DomainException("Coupon code '{$command->code}' already exists.");
        }

        $coupon = new EloquentCoupon();
        $coupon->code                     = $command->code;
        $coupon->description              = $command->description;
        $coupon->type                     = $command->type;
        $coupon->usage_limit              = $command->usage_limit;
        $coupon->usage_limit_per_customer = $command->usage_limit_per_customer;
        $coupon->cart_rule_id             = $command->cart_rule_id;
        $coupon->is_active                = $command->is_active;
        $coupon->valid_from               = $command->valid_from;
        $coupon->valid_until              = $command->valid_until;
        $coupon->times_used               = 0;
        $coupon->save();

        return $coupon;
    }
}
