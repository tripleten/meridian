<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Domain\Source
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Domain\Source;

enum SourceType: string
{
    case Warehouse = 'warehouse';
    case Store     = 'store';
    case Dropship  = 'dropship';

    public function label(): string
    {
        return match($this) {
            self::Warehouse => 'Warehouse',
            self::Store     => 'Store',
            self::Dropship  => 'Dropship',
        };
    }
}
