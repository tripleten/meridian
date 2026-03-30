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

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Meridian\IdentityAccess\Application\DTOs\AdminUserData;
use Meridian\IdentityAccess\Domain\User\UserRole;

final readonly class ListAdminUsersHandler
{
    public function handle(ListAdminUsersQuery $query): LengthAwarePaginator
    {
        $builder = User::with('roles')
            ->whereHas('roles', fn ($q) => $q->whereIn('name', UserRole::adminRoleNames()))
            ->orderBy('name');

        if ($query->search !== '') {
            $builder->where(function ($q) use ($query): void {
                $q->where('name', 'like', "%{$query->search}%")
                  ->orWhere('email', 'like', "%{$query->search}%");
            });
        }

        if ($query->role !== '') {
            $builder->whereHas('roles', fn ($q) => $q->where('name', $query->role));
        }

        return $builder->paginate($query->perPage)
            ->through(fn (User $user) => AdminUserData::fromModel($user));
    }
}
