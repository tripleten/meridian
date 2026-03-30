<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Inventory\Application\Commands\AdjustStockCommand;
use Meridian\Inventory\Application\Commands\AdjustStockHandler;
use Meridian\Inventory\Application\DTOs\InventoryItemData;
use Meridian\Inventory\Application\Queries\ListInventorySourcesHandler;
use Meridian\Inventory\Application\Queries\ListStockHandler;
use Meridian\Inventory\Application\Queries\ListStockQuery;
use Meridian\Inventory\Infrastructure\Persistence\EloquentInventoryItem;

final class StockController
{
    public function index(
        Request $request,
        ListStockHandler $handler,
        ListInventorySourcesHandler $sourcesHandler,
    ): Response {
        return Inertia::render('admin/inventory/stock/index', [
            'items'   => $handler->handle(new ListStockQuery(
                search:         $request->string('search')->trim()->value(),
                source_id:      $request->string('source_id')->value(),
                low_stock_only: (bool) $request->input('low_stock_only'),
                perPage:        50,
            )),
            'filters' => $request->only('search', 'source_id', 'low_stock_only'),
            'sources' => $sourcesHandler->handle(),
        ]);
    }

    public function edit(string $stock): Response
    {
        $item = EloquentInventoryItem::with(['productVariant.product', 'inventorySource'])
            ->findOrFail($stock);

        return Inertia::render('admin/inventory/stock/edit', [
            'item' => InventoryItemData::fromModel($item),
        ]);
    }

    public function update(string $stock, Request $request, AdjustStockHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'qty_available'       => ['required', 'integer', 'min:0'],
            'qty_incoming'        => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'backorders_allowed'  => ['nullable', 'boolean'],
            'manage_stock'        => ['nullable', 'boolean'],
        ]);

        $item = EloquentInventoryItem::findOrFail($stock);

        $handler->handle(new AdjustStockCommand(
            product_variant_id:  $item->product_variant_id,
            source_id:           $item->source_id,
            qty_available:       (int) $validated['qty_available'],
            qty_incoming:        (int) ($validated['qty_incoming'] ?? 0),
            low_stock_threshold: (int) ($validated['low_stock_threshold'] ?? 5),
            backorders_allowed:  (bool) ($validated['backorders_allowed'] ?? false),
            manage_stock:        (bool) ($validated['manage_stock'] ?? true),
        ));

        return redirect()->route('admin.inventory.stock.index')->with('success', 'Stock adjusted.');
    }
}
