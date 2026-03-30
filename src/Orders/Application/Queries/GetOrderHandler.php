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

use Meridian\Orders\Infrastructure\Persistence\EloquentOrder;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class GetOrderHandler
{
    public function handle(GetOrderQuery $query): EloquentOrder
    {
        $order = EloquentOrder::with(['items', 'comments', 'refunds'])
            ->find($query->orderId);

        if ($order === null) {
            throw new DomainException("Order '{$query->orderId}' not found.");
        }

        return $order;
    }
}
