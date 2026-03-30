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

enum GiftCardState: string
{
    case Active    = 'active';
    case Redeemed  = 'redeemed';
    case Expired   = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active    => 'Active',
            self::Redeemed  => 'Redeemed',
            self::Expired   => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Active    => 'default',
            self::Redeemed  => 'secondary',
            self::Expired   => 'outline',
            self::Cancelled => 'destructive',
        };
    }
}
