<?php

declare(strict_types=1);

namespace Meridian\Webhooks\Application\Commands;

use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Webhooks\Infrastructure\Persistence\EloquentWebhookEndpoint;

final class UpdateWebhookEndpointHandler
{
    public function handle(UpdateWebhookEndpointCommand $command): void
    {
        $endpoint = EloquentWebhookEndpoint::find($command->id);

        if ($endpoint === null) {
            throw new DomainException("Webhook endpoint [{$command->id}] not found.");
        }

        $endpoint->url               = $command->url;
        $endpoint->is_active         = $command->is_active;
        $endpoint->subscribed_events = $command->subscribed_events;

        if ($command->secret !== null && $command->secret !== '') {
            $endpoint->secret = hash('sha256', $command->secret);
        }

        $endpoint->save();
    }
}
