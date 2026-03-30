<?php

declare(strict_types=1);

namespace Meridian\Webhooks\Application\Commands;

final class UpdateWebhookEndpointCommand
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $url,
        public readonly ?string $secret,  // null = keep existing
        public readonly bool    $is_active,
        public readonly array   $subscribed_events,
    ) {}
}
