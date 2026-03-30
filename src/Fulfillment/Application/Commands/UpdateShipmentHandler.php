<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Application\Commands;

use Meridian\Fulfillment\Domain\ShipmentState;
use Meridian\Fulfillment\Infrastructure\Persistence\EloquentShipment;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Shared\Domain\Exceptions\InvalidStateTransition;

final class UpdateShipmentHandler
{
    public function handle(UpdateShipmentCommand $command): void
    {
        $shipment = EloquentShipment::find($command->shipmentId);

        if ($shipment === null) {
            throw new DomainException("Shipment [{$command->shipmentId}] not found.");
        }

        $current = $shipment->state instanceof ShipmentState
            ? $shipment->state
            : ShipmentState::from($shipment->state);

        $new = ShipmentState::from($command->state);

        $allowed = array_map(fn (ShipmentState $s) => $s->value, $current->allowedTransitions());

        if ($new !== $current && ! in_array($new->value, $allowed, true)) {
            throw new InvalidStateTransition(
                "Cannot transition shipment from [{$current->value}] to [{$new->value}]."
            );
        }

        $shipment->state           = $new->value;
        $shipment->carrier         = $command->carrier;
        $shipment->tracking_number = $command->tracking_number;
        $shipment->tracking_url    = $command->tracking_url;
        $shipment->notes           = $command->notes;

        if ($new === ShipmentState::Shipped && $shipment->shipped_at === null) {
            $shipment->shipped_at = now();
        }

        if ($new === ShipmentState::Delivered && $shipment->delivered_at === null) {
            $shipment->delivered_at = now();
        }

        $shipment->save();
    }
}
