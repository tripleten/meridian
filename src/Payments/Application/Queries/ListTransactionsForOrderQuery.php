<?php

declare(strict_types=1);

namespace Meridian\Payments\Application\Queries;

final class ListTransactionsForOrderQuery
{
    public function __construct(
        public readonly string $orderId,
    ) {}
}
