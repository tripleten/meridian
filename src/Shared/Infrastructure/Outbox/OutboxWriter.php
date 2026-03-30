<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Shared\Infrastructure\Outbox
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Shared\Infrastructure\Outbox;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Meridian\Shared\Domain\Events\DomainEvent;

/**
 * Writes domain events to the outbox_messages table.
 *
 * MUST be called within the same DB transaction as the state change.
 * The OutboxRelay command will pick up unprocessed rows and dispatch them
 * to the appropriate queue jobs after the transaction commits.
 *
 * Usage:
 *   DB::transaction(function () use ($outbox) {
 *       $this->repository->save($aggregate);
 *       $outbox->record(new OrderPlaced($order->id));  // same transaction
 *   });
 */
final class OutboxWriter
{
    /**
     * Record a domain event in the outbox within the current DB transaction.
     *
     * @throws \RuntimeException if called outside a transaction
     */
    public function record(DomainEvent $event): void
    {
        DB::table('outbox_messages')->insert([
            'id'             => (string) Str::ulid(),
            'aggregate_type' => $event->aggregateType(),
            'aggregate_id'   => $event->aggregateId(),
            'event_type'     => $event::class,
            'payload'        => json_encode($event->toPayload(), JSON_THROW_ON_ERROR),
            'occurred_at'    => $event->occurredAt->format('Y-m-d H:i:s'),
            'dispatched_at'  => null,
            'attempts'       => 0,
        ]);
    }
}
