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

final readonly class InviteAdminUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $role,     // UserRole value string
        public int    $invitedBy, // users.id of the inviting admin
    ) {}
}
