<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Infrastructure\Persistence;

use App\Models\User;
use Meridian\IdentityAccess\Domain\Repositories\UserRepositoryInterface;
use Meridian\IdentityAccess\Domain\User\UserRole;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findAdminUsers(string $search = '', int $perPage = 20): mixed
    {
        return User::with('roles')
            ->whereHas('roles', fn ($q) => $q->whereIn('name', UserRole::adminRoleNames()))
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(string $name, string $email, string $hashedPassword): User
    {
        return User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashedPassword,
        ]);
    }

    public function assignRole(User $user, UserRole $role): void
    {
        $user->assignRole($role->value);
    }

    public function revokeRole(User $user, UserRole $role): void
    {
        $user->removeRole($role->value);
    }

    public function syncRoles(User $user, array $roles): void
    {
        $roleNames = array_map(
            fn (UserRole $role) => $role->value,
            $roles,
        );
        $user->syncRoles($roleNames);
    }

    public function deactivate(User $user): void
    {
        // Revoke all roles — user can no longer log in to admin
        $user->syncRoles([UserRole::Customer->value]);
    }
}
