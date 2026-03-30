<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Fulfillment\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Fulfillment\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Fulfillment\Application\Commands\CreateShipmentCommand;
use Meridian\Fulfillment\Application\Commands\CreateShipmentHandler;
use Meridian\Fulfillment\Application\Commands\UpdateShipmentCommand;
use Meridian\Fulfillment\Application\Commands\UpdateShipmentHandler;
use Meridian\Fulfillment\Application\DTOs\ShipmentData;
use Meridian\Fulfillment\Application\Queries\ListShipmentsForOrderHandler;
use Meridian\Fulfillment\Application\Queries\ListShipmentsForOrderQuery;
use Meridian\Fulfillment\Domain\ShipmentState;
use Meridian\Fulfillment\Infrastructure\Persistence\EloquentShipment;
use Meridian\Orders\Application\Queries\GetOrderHandler;
use Meridian\Orders\Application\Queries\GetOrderQuery;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Shared\Domain\Exceptions\InvalidStateTransition;

final class ShipmentController
{
    public function index(string $order, ListShipmentsForOrderHandler $handler): Response
    {
        $shipments = $handler->handle(new ListShipmentsForOrderQuery($order));

        return Inertia::render('admin/orders/shipments/index', [
            'orderId'   => $order,
            'shipments' => $shipments->map(fn ($s) => ShipmentData::fromModel($s))->values()->all(),
        ]);
    }

    public function create(string $order, GetOrderHandler $orderHandler): Response
    {
        $orderModel = $orderHandler->handle(new GetOrderQuery($order));

        return Inertia::render('admin/orders/shipments/create', [
            'orderId'    => $order,
            'orderItems' => $orderModel->items->map(fn ($i) => [
                'id'                  => $i->id,
                'sku'                 => $i->sku,
                'name'                => $i->name,
                'quantity'            => $i->quantity,
                'quantity_refunded'   => $i->quantity_refunded ?? 0,
            ])->values()->all(),
        ]);
    }

    public function store(string $order, Request $request, CreateShipmentHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'carrier'         => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'tracking_url'    => ['nullable', 'url', 'max:500'],
            'notes'           => ['nullable', 'string', 'max:1000'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.order_item_id'    => ['required', 'string'],
            'items.*.sku'              => ['required', 'string'],
            'items.*.name'             => ['required', 'string'],
            'items.*.quantity_shipped' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $handler->handle(new CreateShipmentCommand(
                orderId:         $order,
                carrier:         $validated['carrier'] ?? null,
                tracking_number: $validated['tracking_number'] ?? null,
                tracking_url:    $validated['tracking_url'] ?? null,
                notes:           $validated['notes'] ?? null,
                items:           $validated['items'],
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('admin.orders.shipments.index', $order)->with('success', 'Shipment created.');
    }

    public function edit(string $order, string $shipment): Response
    {
        $model = EloquentShipment::with('items')->findOrFail($shipment);
        $data  = ShipmentData::fromModel($model);

        $current = $model->state instanceof ShipmentState
            ? $model->state
            : ShipmentState::from($model->state);

        return Inertia::render('admin/orders/shipments/edit', [
            'orderId'            => $order,
            'shipment'           => $data,
            'allowedTransitions' => array_map(
                fn (ShipmentState $s) => ['value' => $s->value, 'label' => $s->label()],
                $current->allowedTransitions(),
            ),
        ]);
    }

    public function update(string $order, string $shipment, Request $request, UpdateShipmentHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'state'           => ['required', 'string'],
            'carrier'         => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'tracking_url'    => ['nullable', 'url', 'max:500'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $handler->handle(new UpdateShipmentCommand(
                shipmentId:      $shipment,
                state:           $validated['state'],
                carrier:         $validated['carrier'] ?? null,
                tracking_number: $validated['tracking_number'] ?? null,
                tracking_url:    $validated['tracking_url'] ?? null,
                notes:           $validated['notes'] ?? null,
            ));
        } catch (DomainException | InvalidStateTransition $e) {
            return back()->withErrors(['state' => $e->getMessage()]);
        }

        return redirect()->route('admin.orders.shipments.index', $order)->with('success', 'Shipment updated.');
    }
}
