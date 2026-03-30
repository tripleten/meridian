<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Domain\Product
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Domain\Product;

enum ProductType: string
{
    case Simple       = 'simple';
    case Configurable = 'configurable';
    case Bundle       = 'bundle';
    case Virtual      = 'virtual';
    case Downloadable = 'downloadable';

    public function label(): string
    {
        return match ($this) {
            self::Simple       => 'Simple',
            self::Configurable => 'Configurable',
            self::Bundle       => 'Bundle',
            self::Virtual      => 'Virtual',
            self::Downloadable => 'Downloadable',
        };
    }

    public function hasVariants(): bool
    {
        return $this === self::Configurable;
    }

    public function requiresShipping(): bool
    {
        return !in_array($this, [self::Virtual, self::Downloadable]);
    }
}
