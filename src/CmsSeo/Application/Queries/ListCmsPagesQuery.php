<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Application\Queries;

final readonly class ListCmsPagesQuery
{
    public function __construct(
        public string $search  = '',
        public string $state   = '',
        public int    $perPage = 20,
    ) {}
}
