<?php

declare(strict_types=1);

namespace Meridian\Payments\Application\Queries;

use Illuminate\Support\Collection;
use Meridian\Payments\Infrastructure\Persistence\EloquentTransaction;

final class ListTransactionsForOrderHandler
{
    public function handle(ListTransactionsForOrderQuery $query): Collection
    {
        return EloquentTransaction::where('order_id', $query->orderId)
            ->orderByDesc('created_at')
            ->get();
    }
}
