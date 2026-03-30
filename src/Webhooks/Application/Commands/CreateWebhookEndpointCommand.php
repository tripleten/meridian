<?php

declare(strict_types=1);

namespace Meridian\Webhooks\Application\Commands;

final class CreateWebhookEndpointCommand
{
    public function __construct(
        public readonly string $url,
        public readonly string $secret,
        public readonly bool   $is_active,
        public readonly array  $subscribed_events,
    ) {}
}
