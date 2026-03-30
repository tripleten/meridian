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

enum CatalogDiscountType: string
{
    case Percentage = 'percentage';
    case Fixed      = 'fixed';
    case ToFixed    = 'to_fixed';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Percentage',
            self::Fixed      => 'Fixed Amount',
            self::ToFixed    => 'Fixed Price',
        };
    }
}
