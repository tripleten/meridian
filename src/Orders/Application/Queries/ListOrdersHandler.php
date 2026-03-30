<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Application\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Meridian\Orders\Application\DTOs\OrderData;
use Meridian\Orders\Infrastructure\Persistence\EloquentOrder;

final readonly class ListOrdersHandler
{
    public function handle(ListOrdersQuery $query): LengthAwarePaginator
    {
        $builder = EloquentOrder::withCount('items');

        if ($query->search !== '') {
            $term = '%' . $query->search . '%';
            $builder->where(function ($q) use ($term): void {
                $q->where('number', 'like', $term)
                  ->orWhere('customer_email', 'like', $term);
            });
        }

        if ($query->status !== '') {
            $builder->where('status', $query->status);
        }

        if ($query->payment_status !== '') {
            $builder->where('payment_status', $query->payment_status);
        }

        return $builder
            ->orderByRaw('COALESCE(placed_at, created_at) DESC')
            ->paginate($query->perPage)
            ->through(fn (object $order) => OrderData::fromModel($order));
    }
}
