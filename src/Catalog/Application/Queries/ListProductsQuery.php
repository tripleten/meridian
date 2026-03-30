<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\Queries;

final readonly class ListProductsQuery
{
    public function __construct(
        public string  $search   = '',
        public string  $status   = '',
        public string  $type     = '',
        public ?string $brand_id = null,
        public int     $perPage  = 20,
    ) {}
}
