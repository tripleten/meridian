<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Domain;

enum ShipmentState: string
{
    case Pending   = 'pending';
    case Shipped   = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pending',
            self::Shipped   => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::Pending   => 'secondary',
            self::Shipped   => 'default',
            self::Delivered => 'outline',
            self::Cancelled => 'destructive',
        };
    }

    /** @return ShipmentState[] */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::Pending   => [self::Shipped, self::Cancelled],
            self::Shipped   => [self::Delivered, self::Cancelled],
            self::Delivered => [],
            self::Cancelled => [],
        };
    }
}
