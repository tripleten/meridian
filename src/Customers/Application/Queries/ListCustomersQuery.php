<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Application\Queries;

final readonly class ListCustomersQuery
{
    public function __construct(
        public string $search   = '',
        public string $group_id = '',
        public ?bool  $is_active = null,
        public int    $perPage  = 20,
    ) {}
}
