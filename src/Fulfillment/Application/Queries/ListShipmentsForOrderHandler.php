<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Application\Queries;

use Illuminate\Support\Collection;
use Meridian\Fulfillment\Infrastructure\Persistence\EloquentShipment;

final class ListShipmentsForOrderHandler
{
    public function handle(ListShipmentsForOrderQuery $query): Collection
    {
        return EloquentShipment::with('items')
            ->where('order_id', $query->orderId)
            ->orderByDesc('created_at')
            ->get();
    }
}
