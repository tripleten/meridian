<?php

declare(strict_types=1);

namespace Meridian\Webhooks\Application\DTOs;

use Spatie\LaravelData\Data;

class WebhookEndpointData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly bool   $is_active,
        public readonly array  $subscribed_events,
        public readonly int    $delivery_count,
        public readonly string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:                $model->id,
            url:               $model->url,
            is_active:         (bool) $model->is_active,
            subscribed_events: $model->subscribed_events ?? [],
            delivery_count:    (int) ($model->webhook_deliveries_count ?? 0),
            created_at:        $model->created_at?->toIso8601String() ?? '',
        );
    }
}
