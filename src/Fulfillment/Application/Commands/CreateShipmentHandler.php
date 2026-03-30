<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Application\Commands;

use Illuminate\Support\Facades\DB;
use Meridian\Fulfillment\Domain\ShipmentState;
use Meridian\Fulfillment\Infrastructure\Persistence\EloquentShipment;
use Meridian\Fulfillment\Infrastructure\Persistence\EloquentShipmentItem;

final class CreateShipmentHandler
{
    public function handle(CreateShipmentCommand $command): EloquentShipment
    {
        return DB::transaction(function () use ($command): EloquentShipment {
            $shipment = EloquentShipment::create([
                'order_id'        => $command->orderId,
                'state'           => ShipmentState::Pending->value,
                'carrier'         => $command->carrier,
                'tracking_number' => $command->tracking_number,
                'tracking_url'    => $command->tracking_url,
                'notes'           => $command->notes,
            ]);

            foreach ($command->items as $item) {
                EloquentShipmentItem::create([
                    'shipment_id'       => $shipment->id,
                    'order_item_id'     => $item['order_item_id'],
                    'sku'               => $item['sku'],
                    'name'              => $item['name'],
                    'quantity_shipped'  => (int) $item['quantity_shipped'],
                ]);
            }

            return $shipment->load('items');
        });
    }
}
