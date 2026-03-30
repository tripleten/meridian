<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\Commands;

final readonly class AdjustStockCommand
{
    public function __construct(
        public string $product_variant_id,
        public string $source_id,
        public int    $qty_available,
        public int    $qty_incoming          = 0,
        public int    $low_stock_threshold   = 5,
        public bool   $backorders_allowed    = false,
        public bool   $manage_stock         = true,
    ) {}
}
