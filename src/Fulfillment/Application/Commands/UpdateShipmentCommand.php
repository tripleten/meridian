<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Application\Commands;

final class UpdateShipmentCommand
{
    public function __construct(
        public readonly string  $shipmentId,
        public readonly string  $state,
        public readonly ?string $carrier,
        public readonly ?string $tracking_number,
        public readonly ?string $tracking_url,
        public readonly ?string $notes,
    ) {}
}
