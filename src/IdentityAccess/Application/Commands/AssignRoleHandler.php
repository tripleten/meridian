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

use Meridian\IdentityAccess\Domain\Repositories\UserRepositoryInterface;
use Meridian\IdentityAccess\Domain\User\UserRole;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class AssignRoleHandler
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function handle(AssignRoleCommand $command): void
    {
        $user = $this->users->findById($command->userId)
            ?? throw new DomainException("User {$command->userId} not found.");

        $role = UserRole::tryFrom($command->role)
            ?? throw new DomainException("Unknown role: {$command->role}");

        $this->users->assignRole($user, $role);
    }
}
