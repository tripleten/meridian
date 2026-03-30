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

use Meridian\IdentityAccess\Application\DTOs\AdminUserData;
use Meridian\IdentityAccess\Domain\Repositories\UserRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class GetAdminUserHandler
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function handle(GetAdminUserQuery $query): AdminUserData
    {
        $user = $this->users->findById($query->userId)
            ?? throw new DomainException("User {$query->userId} not found.");

        return AdminUserData::fromModel($user);
    }
}
