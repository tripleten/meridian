<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Shared\Domain\Exceptions
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Shared\Domain\Exceptions;

/**
 * Thrown when a state machine transition is not permitted from the current state.
 *
 * e.g. Attempting to transition an Order from Shipped → PendingPayment.
 */
final class InvalidStateTransition extends DomainException
{
    public static function for(
        string $aggregateType,
        string $fromState,
        string $toState,
    ): self {
        return new self(
            "Cannot transition {$aggregateType} from '{$fromState}' to '{$toState}'."
        );
    }
}
