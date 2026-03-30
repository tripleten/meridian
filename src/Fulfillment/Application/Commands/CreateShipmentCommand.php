<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Application\Commands;

final class CreateShipmentCommand
{
    public function __construct(
        public readonly string  $orderId,
        public readonly ?string $carrier,
        public readonly ?string $tracking_number,
        public readonly ?string $tracking_url,
        public readonly ?string $notes,
        public readonly array   $items,
    ) {}
}
