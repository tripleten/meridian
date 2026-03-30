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

use Meridian\Customers\Domain\Repositories\CustomerRepositoryInterface;

final class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(string $id): ?object
    {
        return EloquentCustomer::with(['user', 'customerGroup'])->find($id);
    }

    public function findByUserId(int $userId): ?object
    {
        return EloquentCustomer::with(['user', 'customerGroup'])
            ->where('user_id', $userId)
            ->first();
    }

    public function paginate(array $filters, int $perPage): mixed
    {
        $builder = EloquentCustomer::with(['user', 'customerGroup'])
            ->withCount('addresses');

        if (! empty($filters['search'])) {
            $term = "%{$filters['search']}%";
            $builder->where(function ($q) use ($term): void {
                $q->where('first_name', 'like', $term)
                  ->orWhere('last_name', 'like', $term)
                  ->orWhereHas('user', fn ($uq) => $uq->where('email', 'like', $term));
            });
        }

        if (! empty($filters['group_id'])) {
            $builder->where('customer_group_id', $filters['group_id']);
        }

        if (isset($filters['is_active'])) {
            $builder->where('is_active', $filters['is_active']);
        }

        return $builder->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function save(object $customer): void
    {
        /** @var EloquentCustomer $customer */
        $customer->save();
    }

    public function delete(string $id): void
    {
        EloquentCustomer::destroy($id);
    }
}
