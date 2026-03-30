<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Application\Queries;

final readonly class ListOrdersQuery
{
    public function __construct(
        public string $search         = '',
        public string $status         = '',
        public string $payment_status = '',
        public int    $perPage        = 20,
    ) {}
}
