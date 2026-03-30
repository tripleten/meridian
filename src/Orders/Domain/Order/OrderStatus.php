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

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Processing     = 'processing';
    case OnHold         = 'on_hold';
    case Shipped        = 'shipped';
    case Delivered      = 'delivered';
    case Cancelled      = 'cancelled';
    case Refunded       = 'refunded';
    case PartialRefund  = 'partial_refund';

    public function label(): string
    {
        return match($this) {
            self::PendingPayment => 'Pending Payment',
            self::Processing     => 'Processing',
            self::OnHold         => 'On Hold',
            self::Shipped        => 'Shipped',
            self::Delivered      => 'Delivered',
            self::Cancelled      => 'Cancelled',
            self::Refunded       => 'Refunded',
            self::PartialRefund  => 'Partial Refund',
        };
    }

    public function allowedTransitions(): array
    {
        return match($this) {
            self::PendingPayment => [self::Processing, self::Cancelled],
            self::Processing     => [self::OnHold, self::Shipped, self::Cancelled],
            self::OnHold         => [self::Processing, self::Cancelled],
            self::Shipped        => [self::Delivered, self::PartialRefund, self::Refunded],
            self::Delivered      => [self::PartialRefund, self::Refunded],
            self::Cancelled      => [],
            self::Refunded       => [],
            self::PartialRefund  => [self::Refunded],
        };
    }

    public function canTransitionTo(self $new): bool
    {
        return in_array($new, $this->allowedTransitions(), true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Cancelled, self::Refunded], true);
    }
}
