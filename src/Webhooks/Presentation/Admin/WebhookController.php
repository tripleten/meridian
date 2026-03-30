<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Webhooks\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Webhooks\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Webhooks\Application\Commands\CreateWebhookEndpointCommand;
use Meridian\Webhooks\Application\Commands\CreateWebhookEndpointHandler;
use Meridian\Webhooks\Application\Commands\UpdateWebhookEndpointCommand;
use Meridian\Webhooks\Application\Commands\UpdateWebhookEndpointHandler;
use Meridian\Webhooks\Application\DTOs\WebhookDeliveryData;
use Meridian\Webhooks\Application\DTOs\WebhookEndpointData;
use Meridian\Webhooks\Infrastructure\Persistence\EloquentWebhookDelivery;
use Meridian\Webhooks\Infrastructure\Persistence\EloquentWebhookEndpoint;

final class WebhookController
{
    private const KNOWN_EVENTS = [
        'order.placed',
        'order.status_changed',
        'order.shipped',
        'order.delivered',
        'order.cancelled',
        'order.refunded',
        'customer.created',
        'customer.updated',
        'product.created',
        'product.updated',
        'product.deleted',
        'payment.captured',
        'payment.refunded',
    ];

    public function index(): Response
    {
        $endpoints = EloquentWebhookEndpoint::withCount('deliveries')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('admin/webhooks/index', [
            'endpoints' => $endpoints->map(fn ($e) => WebhookEndpointData::fromModel($e))->values()->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/webhooks/create', [
            'knownEvents'    => self::KNOWN_EVENTS,
            'generatedSecret' => Str::random(40),
        ]);
    }

    public function store(Request $request, CreateWebhookEndpointHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'url'               => ['required', 'url', 'max:500'],
            'secret'            => ['required', 'string', 'min:16'],
            'is_active'         => ['required', 'boolean'],
            'subscribed_events' => ['required', 'array', 'min:1'],
            'subscribed_events.*' => ['string'],
        ]);

        $handler->handle(new CreateWebhookEndpointCommand(
            url:               $validated['url'],
            secret:            $validated['secret'],
            is_active:         (bool) $validated['is_active'],
            subscribed_events: $validated['subscribed_events'],
        ));

        return redirect('/admin/webhooks')->with('success', 'Webhook endpoint created.');
    }

    public function edit(string $webhook): Response
    {
        $model = EloquentWebhookEndpoint::findOrFail($webhook);

        return Inertia::render('admin/webhooks/edit', [
            'endpoint'    => WebhookEndpointData::fromModel($model),
            'knownEvents' => self::KNOWN_EVENTS,
        ]);
    }

    public function update(string $webhook, Request $request, UpdateWebhookEndpointHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'url'               => ['required', 'url', 'max:500'],
            'secret'            => ['nullable', 'string', 'min:16'],
            'is_active'         => ['required', 'boolean'],
            'subscribed_events' => ['required', 'array', 'min:1'],
            'subscribed_events.*' => ['string'],
        ]);

        $handler->handle(new UpdateWebhookEndpointCommand(
            id:                $webhook,
            url:               $validated['url'],
            secret:            $validated['secret'] ?? null,
            is_active:         (bool) $validated['is_active'],
            subscribed_events: $validated['subscribed_events'],
        ));

        return redirect('/admin/webhooks')->with('success', 'Webhook endpoint updated.');
    }

    public function destroy(string $webhook): RedirectResponse
    {
        EloquentWebhookEndpoint::findOrFail($webhook)->delete();

        return redirect('/admin/webhooks')->with('success', 'Webhook endpoint deleted.');
    }

    public function deliveries(string $webhook): Response
    {
        $endpoint  = EloquentWebhookEndpoint::findOrFail($webhook);
        $deliveries = EloquentWebhookDelivery::where('endpoint_id', $webhook)
            ->orderByDesc('created_at')
            ->paginate(30);

        return Inertia::render('admin/webhooks/deliveries', [
            'endpoint'   => WebhookEndpointData::fromModel($endpoint),
            'deliveries' => $deliveries->through(fn ($d) => WebhookDeliveryData::fromModel($d)),
        ]);
    }
}
