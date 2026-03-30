<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Infrastructure\Persistence;

use Meridian\Inventory\Domain\Repositories\InventoryItemRepositoryInterface;

final class EloquentInventoryItemRepository implements InventoryItemRepositoryInterface
{
    public function findByVariantAndSource(string $variantId, string $sourceId): ?EloquentInventoryItem
    {
        return EloquentInventoryItem::where('product_variant_id', $variantId)
            ->where('source_id', $sourceId)
            ->first();
    }

    public function findByVariant(string $variantId): array
    {
        return EloquentInventoryItem::with('inventorySource')
            ->where('product_variant_id', $variantId)
            ->get()
            ->all();
    }

    public function save(object $item): void
    {
        $item->save();
    }
}
