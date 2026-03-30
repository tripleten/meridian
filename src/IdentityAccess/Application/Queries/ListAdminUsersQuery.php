<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Application\Queries;

final readonly class ListAdminUsersQuery
{
    public function __construct(
        public string $search  = '',
        public string $role    = '',
        public int    $perPage = 20,
    ) {}
}
