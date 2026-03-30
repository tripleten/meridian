<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Shared\Domain\Events
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Shared\Domain\Events;

use DateTimeImmutable;

/**
 * Base class for all domain events.
 *
 * Domain events are plain PHP objects — no framework dependencies.
 * They are recorded on aggregates and written to the outbox table
 * within the same DB transaction as the state change.
 *
 * Never dispatch domain events directly via event(). Use OutboxWriter.
 */
abstract class DomainEvent
{
    public readonly DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new DateTimeImmutable();
    }

    /**
     * The type of aggregate that raised this event (e.g. 'Order', 'Product').
     */
    abstract public function aggregateType(): string;

    /**
     * The ULID of the aggregate instance that raised this event.
     */
    abstract public function aggregateId(): string;

    /**
     * Serialise the event payload for outbox storage.
     *
     * @return array<string, mixed>
     */
    abstract public function toPayload(): array;
}
