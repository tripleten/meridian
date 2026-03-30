<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Domain\Repositories
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Domain\Repositories;

use App\Models\User;
use Meridian\IdentityAccess\Domain\User\UserRole;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    /** @return User[] */
    public function findAdminUsers(string $search = '', int $perPage = 20): mixed;

    public function create(string $name, string $email, string $hashedPassword): User;

    public function assignRole(User $user, UserRole $role): void;

    public function revokeRole(User $user, UserRole $role): void;

    public function syncRoles(User $user, array $roles): void;

    public function deactivate(User $user): void;
}
