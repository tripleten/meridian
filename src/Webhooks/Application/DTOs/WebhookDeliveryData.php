<?php

declare(strict_types=1);

namespace Meridian\Webhooks\Application\DTOs;

use Spatie\LaravelData\Data;

class WebhookDeliveryData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $endpoint_id,
        public readonly string  $event_type,
        public readonly ?int    $response_status,
        public readonly int     $attempt_count,
        public readonly string  $state,
        public readonly ?string $next_retry_at,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:              $model->id,
            endpoint_id:     $model->endpoint_id,
            event_type:      $model->event_type,
            response_status: $model->response_status ? (int) $model->response_status : null,
            attempt_count:   (int) $model->attempt_count,
            state:           $model->state,
            next_retry_at:   $model->next_retry_at?->toIso8601String(),
            created_at:      $model->created_at?->toIso8601String() ?? '',
        );
    }
}
