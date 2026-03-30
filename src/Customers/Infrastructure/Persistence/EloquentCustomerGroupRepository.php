<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Infrastructure\Persistence;

use Meridian\Customers\Domain\Repositories\CustomerGroupRepositoryInterface;

final class EloquentCustomerGroupRepository implements CustomerGroupRepositoryInterface
{
    public function findById(string $id): ?object
    {
        return EloquentCustomerGroup::find($id);
    }

    public function findByCode(string $code): ?object
    {
        return EloquentCustomerGroup::where('code', $code)->first();
    }

    public function all(): array
    {
        return EloquentCustomerGroup::withCount('customers')
            ->orderBy('name')
            ->get()
            ->all();
    }

    public function save(object $group): void
    {
        /** @var EloquentCustomerGroup $group */
        $group->save();
    }
}
