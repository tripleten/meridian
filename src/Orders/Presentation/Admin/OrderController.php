<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Orders\Application\Commands\AddOrderCommentCommand;
use Meridian\Orders\Application\Commands\AddOrderCommentHandler;
use Meridian\Orders\Application\Commands\ChangeOrderStatusCommand;
use Meridian\Orders\Application\Commands\ChangeOrderStatusHandler;
use Meridian\Orders\Application\DTOs\OrderCommentData;
use Meridian\Orders\Application\DTOs\OrderDetailData;
use Meridian\Orders\Application\DTOs\OrderItemData;
use Meridian\Orders\Application\Queries\GetOrderHandler;
use Meridian\Orders\Application\Queries\GetOrderQuery;
use Meridian\Orders\Application\Queries\ListOrdersHandler;
use Meridian\Orders\Application\Queries\ListOrdersQuery;
use Meridian\Orders\Domain\Order\OrderStatus;
use Meridian\Orders\Domain\Order\PaymentStatus;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Shared\Domain\Exceptions\InvalidStateTransition;

final class OrderController
{
    public function index(Request $request, ListOrdersHandler $handler): Response
    {
        return Inertia::render('admin/orders/index', [
            'orders'               => $handler->handle(new ListOrdersQuery(
                search:         $request->string('search')->trim()->value(),
                status:         $request->string('status')->value(),
                payment_status: $request->string('payment_status')->value(),
                perPage:        20,
            )),
            'filters'              => $request->only('search', 'status', 'payment_status'),
            'statusOptions'        => $this->statusOptions(),
            'paymentStatusOptions' => $this->paymentStatusOptions(),
        ]);
    }

    public function show(string $order, GetOrderHandler $handler): Response
    {
        $model = $handler->handle(new GetOrderQuery($order));

        $currentStatus = $model->status instanceof OrderStatus
            ? $model->status
            : OrderStatus::from($model->status);

        return Inertia::render('admin/orders/show', [
            'order'                => OrderDetailData::fromModel($model),
            'items'                => $model->items->map(fn ($i) => OrderItemData::fromModel($i))->values()->all(),
            'comments'             => $model->comments->map(fn ($c) => OrderCommentData::fromModel($c))->values()->all(),
            'allowedTransitions'   => array_map(
                fn (OrderStatus $s) => ['value' => $s->value, 'label' => $s->label()],
                $currentStatus->allowedTransitions(),
            ),
            'statusOptions'        => $this->statusOptions(),
            'paymentStatusOptions' => $this->paymentStatusOptions(),
        ]);
    }

    public function updateStatus(string $order, Request $request, ChangeOrderStatusHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'new_status'      => ['required', 'string'],
            'comment'         => ['nullable', 'string', 'max:1000'],
            'notify_customer' => ['nullable', 'boolean'],
        ]);

        try {
            $handler->handle(new ChangeOrderStatusCommand(
                orderId:        $order,
                newStatus:      $validated['new_status'],
                comment:        $validated['comment'] ?? null,
                notifyCustomer: (bool) ($validated['notify_customer'] ?? false),
            ));
        } catch (DomainException | InvalidStateTransition $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('success', 'Order status updated.');
    }

    public function addComment(string $order, Request $request, AddOrderCommentHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'comment'                 => ['required', 'string', 'max:2000'],
            'is_customer_notified'    => ['nullable', 'boolean'],
            'is_visible_to_customer'  => ['nullable', 'boolean'],
        ]);

        try {
            $handler->handle(new AddOrderCommentCommand(
                orderId:                $order,
                comment:                $validated['comment'],
                author_type:            'admin',
                author_id:              auth()->id(),
                is_customer_notified:   (bool) ($validated['is_customer_notified'] ?? false),
                is_visible_to_customer: (bool) ($validated['is_visible_to_customer'] ?? false),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['comment' => $e->getMessage()]);
        }

        return back()->with('success', 'Comment added.');
    }

    private function statusOptions(): array
    {
        return array_map(
            fn (OrderStatus $s) => ['value' => $s->value, 'label' => $s->label()],
            OrderStatus::cases(),
        );
    }

    private function paymentStatusOptions(): array
    {
        return array_map(
            fn (PaymentStatus $s) => ['value' => $s->value, 'label' => $s->label()],
            PaymentStatus::cases(),
        );
    }
}
