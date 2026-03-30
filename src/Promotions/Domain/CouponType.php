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

enum CouponType: string
{
    case SingleUse   = 'single_use';
    case MultiUse    = 'multi_use';
    case PerCustomer = 'per_customer';

    public function label(): string
    {
        return match ($this) {
            self::SingleUse   => 'Single Use',
            self::MultiUse    => 'Multi Use',
            self::PerCustomer => 'Per Customer',
        };
    }
}
