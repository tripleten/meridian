<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Domain\Order
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Domain\Order;

enum PaymentStatus: string
{
    case Pending           = 'pending';
    case Paid              = 'paid';
    case PartiallyPaid     = 'partially_paid';
    case Refunded          = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
    case Failed            = 'failed';

    public function label(): string
    {
        return match($this) {
            self::Pending           => 'Pending',
            self::Paid              => 'Paid',
            self::PartiallyPaid     => 'Partially Paid',
            self::Refunded          => 'Refunded',
            self::PartiallyRefunded => 'Partially Refunded',
            self::Failed            => 'Failed',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::Paid, self::PartiallyPaid              => 'default',
            self::Refunded, self::PartiallyRefunded      => 'secondary',
            self::Failed                                  => 'destructive',
            default                                       => 'outline',
        };
    }
}
