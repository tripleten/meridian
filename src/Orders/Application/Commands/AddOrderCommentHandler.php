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

use Illuminate\Support\Str;
use Meridian\Orders\Domain\Repositories\OrderRepositoryInterface;
use Meridian\Orders\Infrastructure\Persistence\EloquentOrderComment;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class AddOrderCommentHandler
{
    public function __construct(private OrderRepositoryInterface $orders) {}

    public function handle(AddOrderCommentCommand $command): void
    {
        $order = $this->orders->findById($command->orderId);

        if ($order === null) {
            throw new DomainException("Order '{$command->orderId}' not found.");
        }

        EloquentOrderComment::create([
            'id'                      => (string) Str::ulid(),
            'order_id'                => $command->orderId,
            'author_type'             => $command->author_type,
            'author_id'               => $command->author_id,
            'comment'                 => $command->comment,
            'is_customer_notified'    => $command->is_customer_notified,
            'is_visible_to_customer'  => $command->is_visible_to_customer,
        ]);
    }
}
