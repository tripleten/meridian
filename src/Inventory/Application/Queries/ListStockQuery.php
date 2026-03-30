<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\Queries;

final readonly class ListStockQuery
{
    public function __construct(
        public string $search         = '',
        public string $source_id      = '',
        public bool   $low_stock_only = false,
        public int    $perPage        = 50,
    ) {}
}
