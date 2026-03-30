<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\Commands;

use Meridian\Inventory\Infrastructure\Persistence\EloquentInventoryItem;
use Illuminate\Support\Str;

final class AdjustStockHandler
{
    public function handle(AdjustStockCommand $command): EloquentInventoryItem
    {
        $item = EloquentInventoryItem::firstOrNew([
            'product_variant_id' => $command->product_variant_id,
            'source_id'          => $command->source_id,
        ]);

        if (! $item->exists) {
            $item->id = (string) Str::ulid();
        }

        $item->qty_available        = $command->qty_available;
        $item->qty_incoming         = $command->qty_incoming;
        $item->low_stock_threshold  = $command->low_stock_threshold;
        $item->backorders_allowed   = $command->backorders_allowed;
        $item->manage_stock         = $command->manage_stock;
        $item->save();

        return $item;
    }
}
