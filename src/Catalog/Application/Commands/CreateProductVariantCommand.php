<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\Commands;

final readonly class CreateProductVariantCommand
{
    public function __construct(
        public string  $productId,
        public string  $sku,
        public ?string $name        = null,
        public ?int    $price       = null,
        public ?int    $compare_price = null,
        public ?int    $cost_price  = null,
        public ?float  $weight      = null,
        public bool    $is_active   = true,
        public int     $sort_order  = 0,
    ) {}
}
