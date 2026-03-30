<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Application\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Meridian\Customers\Application\DTOs\CustomerData;
use Meridian\Customers\Infrastructure\Persistence\EloquentCustomer;

final readonly class ListCustomersHandler
{
    public function handle(ListCustomersQuery $query): LengthAwarePaginator
    {
        $builder = EloquentCustomer::with(['user', 'customerGroup'])
            ->withCount('addresses');

        if ($query->search !== '') {
            $term = "%{$query->search}%";
            $builder->where(function ($q) use ($term): void {
                $q->where('first_name', 'like', $term)
                  ->orWhere('last_name', 'like', $term)
                  ->orWhereHas('user', fn ($uq) => $uq->where('email', 'like', $term));
            });
        }

        if ($query->group_id !== '') {
            $builder->where('customer_group_id', $query->group_id);
        }

        if ($query->is_active !== null) {
            $builder->where('is_active', $query->is_active);
        }

        return $builder
            ->orderBy('created_at', 'desc')
            ->paginate($query->perPage)
            ->through(fn ($customer) => CustomerData::fromModel($customer));
    }
}
