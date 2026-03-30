<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Application\DTOs;

use Meridian\Fulfillment\Domain\ShipmentState;
use Spatie\LaravelData\Data;

class ShipmentData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $order_id,
        public readonly string  $state,
        public readonly string  $state_label,
        public readonly string  $state_badge,
        public readonly ?string $carrier,
        public readonly ?string $tracking_number,
        public readonly ?string $tracking_url,
        public readonly ?string $shipped_at,
        public readonly ?string $delivered_at,
        public readonly ?string $notes,
        public readonly string  $created_at,
        public readonly array   $items,
    ) {}

    public static function fromModel(object $model): self
    {
        $state = $model->state instanceof ShipmentState
            ? $model->state
            : ShipmentState::from($model->state);

        return new self(
            id:              $model->id,
            order_id:        $model->order_id,
            state:           $state->value,
            state_label:     $state->label(),
            state_badge:     $state->badgeVariant(),
            carrier:         $model->carrier,
            tracking_number: $model->tracking_number,
            tracking_url:    $model->tracking_url,
            shipped_at:      $model->shipped_at?->toIso8601String(),
            delivered_at:    $model->delivered_at?->toIso8601String(),
            notes:           $model->notes,
            created_at:      $model->created_at?->toIso8601String() ?? '',
            items:           $model->relationLoaded('items')
                ? $model->items->map(fn ($i) => [
                    'id'                 => $i->id,
                    'order_item_id'      => $i->order_item_id,
                    'sku'                => $i->sku,
                    'name'               => $i->name,
                    'quantity_shipped'   => $i->quantity_shipped,
                ])->all()
                : [],
        );
    }
}
