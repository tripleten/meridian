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

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Meridian\IdentityAccess\Domain\Events\AdminUserInvited;
use Meridian\IdentityAccess\Domain\Repositories\UserRepositoryInterface;
use Meridian\IdentityAccess\Domain\User\UserRole;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Shared\Infrastructure\Outbox\OutboxWriter;

final readonly class InviteAdminUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private OutboxWriter            $outbox,
    ) {}

    public function handle(InviteAdminUserCommand $command): void
    {
        $role = UserRole::tryFrom($command->role)
            ?? throw new DomainException("Unknown role: {$command->role}");

        if (! $role->isAdminRole()) {
            throw new DomainException("Role '{$command->role}' is not an admin role.");
        }

        if ($this->users->findByEmail($command->email) !== null) {
            throw new DomainException("A user with email '{$command->email}' already exists.");
        }

        DB::transaction(function () use ($command, $role): void {
            $user = $this->users->create(
                name:            $command->name,
                email:           $command->email,
                // Temporary random password — admin must reset via "forgot password"
                hashedPassword:  Hash::make(Str::password(20)),
            );

            $this->users->assignRole($user, $role);

            $this->outbox->record(new AdminUserInvited(
                userId: $user->id,
                email:  $user->email,
                role:   $role->value,
            ));
        });
    }
}
