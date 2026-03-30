<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Domain;

enum ReturnState: string
{
    case Requested = 'requested';
    case Approved  = 'approved';
    case Rejected  = 'rejected';
    case Received  = 'received';
    case Refunded  = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Requested => 'Requested',
            self::Approved  => 'Approved',
            self::Rejected  => 'Rejected',
            self::Received  => 'Received',
            self::Refunded  => 'Refunded',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::Requested => 'secondary',
            self::Approved  => 'default',
            self::Rejected  => 'destructive',
            self::Received  => 'outline',
            self::Refunded  => 'outline',
        };
    }
}
