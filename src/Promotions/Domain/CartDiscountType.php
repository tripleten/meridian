<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Promotions\Domain
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Promotions\Domain;

enum CartDiscountType: string
{
    case Percentage    = 'percentage';
    case FixedCart     = 'fixed_cart';
    case FixedProduct  = 'fixed_product';
    case BuyXGetY      = 'buy_x_get_y';
    case FreeShipping  = 'free_shipping';

    public function label(): string
    {
        return match ($this) {
            self::Percentage   => 'Percentage',
            self::FixedCart    => 'Fixed Cart Discount',
            self::FixedProduct => 'Fixed Product Discount',
            self::BuyXGetY     => 'Buy X Get Y',
            self::FreeShipping => 'Free Shipping',
        };
    }
}
