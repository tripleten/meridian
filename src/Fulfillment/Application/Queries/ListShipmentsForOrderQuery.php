<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Application\Queries;

final class ListShipmentsForOrderQuery
{
    public function __construct(
        public readonly string $orderId,
    ) {}
}
