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

final readonly class UpdateProductCommand
{
    public function __construct(
        public string  $productId,
        public string  $name,
        public string  $type,
        public int     $base_price,
        public string  $url_key,
        public string  $status        = 'draft',
        public ?int    $compare_price = null,
        public ?int    $cost_price    = null,
        public ?string $brand_id      = null,
        public ?string $attribute_set_id = null,
        public ?string $tax_class_id  = null,
        public ?string $short_description = null,
        public ?string $description   = null,
        public ?float  $weight        = null,
        public string  $weight_unit   = 'kg',
        public bool    $is_featured   = false,
        public ?string $main_image    = null,
        public array   $category_ids  = [],
    ) {}
}
