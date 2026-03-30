<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Application\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Meridian\Orders\Domain\Order\OrderStatus;
use Meridian\Orders\Infrastructure\Persistence\EloquentOrder;
use Meridian\Orders\Infrastructure\Persistence\EloquentOrderComment;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Shared\Domain\Exceptions\InvalidStateTransition;

final class ChangeOrderStatusHandler
{
    public function handle(ChangeOrderStatusCommand $command): void
    {
        $order = EloquentOrder::find($command->orderId);

        if ($order === null) {
            throw new DomainException("Order [{$command->orderId}] not found.");
        }

        $current = OrderStatus::from(
            $order->status instanceof OrderStatus
                ? $order->status->value
                : $order->status
        );

        $target = OrderStatus::from($command->newStatus);

        if (! $current->canTransitionTo($target)) {
            throw InvalidStateTransition::for('Order', $current->value, $target->value);
        }

        DB::transaction(function () use ($order, $target, $command): void {
            $order->status = $target->value;
            $order->save();

            if ($command->comment !== null && $command->comment !== '') {
                EloquentOrderComment::create([
                    'order_id'                => $order->id,
                    'author_type'             => 'admin',
                    'author_id'               => Auth::id(),
                    'comment'                 => $command->comment,
                    'is_customer_notified'    => $command->notifyCustomer,
                    'is_visible_to_customer'  => $command->notifyCustomer,
                ]);
            }
        });
    }
}
