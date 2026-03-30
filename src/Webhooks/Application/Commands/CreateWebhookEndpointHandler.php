<?php

declare(strict_types=1);

namespace Meridian\Webhooks\Application\Commands;

use Meridian\Webhooks\Infrastructure\Persistence\EloquentWebhookEndpoint;

final class CreateWebhookEndpointHandler
{
    public function handle(CreateWebhookEndpointCommand $command): EloquentWebhookEndpoint
    {
        return EloquentWebhookEndpoint::create([
            'url'               => $command->url,
            'secret'            => hash('sha256', $command->secret),
            'is_active'         => $command->is_active,
            'subscribed_events' => $command->subscribed_events,
        ]);
    }
}
