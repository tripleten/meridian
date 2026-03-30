<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Application\Commands;

final readonly class UpdateAdminUserCommand
{
    public function __construct(
        public int    $userId,
        public string $name,
        public string $email,
        public string $role,      // single primary role (UserRole value string)
    ) {}
}
