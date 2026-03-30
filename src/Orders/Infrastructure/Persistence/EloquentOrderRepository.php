<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Infrastructure\Persistence;

use Meridian\Orders\Domain\Repositories\OrderRepositoryInterface;

final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function findById(string $id): ?EloquentOrder
    {
        return EloquentOrder::find($id);
    }

    public function findByNumber(string $number): ?EloquentOrder
    {
        return EloquentOrder::where('number', $number)->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): mixed
    {
        return EloquentOrder::withCount('items')
            ->orderByRaw('COALESCE(placed_at, created_at) DESC')
            ->paginate($perPage);
    }

    public function save(object $order): void
    {
        $order->save();
    }
}
