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

final readonly class UpdateAdminUserHandler
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function handle(UpdateAdminUserCommand $command): void
    {
        $user = $this->users->findById($command->userId)
            ?? throw new DomainException("User {$command->userId} not found.");

        $role = UserRole::tryFrom($command->role)
            ?? throw new DomainException("Unknown role: {$command->role}");

        // Protect the last super-admin — cannot have their role changed
        if ($user->hasRole(UserRole::SuperAdmin->value) && $role !== UserRole::SuperAdmin) {
            $superAdminCount = \App\Models\User::role(UserRole::SuperAdmin->value)->count();
            if ($superAdminCount <= 1) {
                throw new DomainException('Cannot change the role of the last super-admin.');
            }
        }

        $user->name  = $command->name;
        $user->email = $command->email;
        $user->save();

        $this->users->syncRoles($user, [$role]);
    }
}
