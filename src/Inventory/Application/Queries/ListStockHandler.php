<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Meridian\Inventory\Application\DTOs\InventoryItemData;
use Meridian\Inventory\Infrastructure\Persistence\EloquentInventoryItem;

final class ListStockHandler
{
    public function handle(ListStockQuery $query): LengthAwarePaginator
    {
        $builder = EloquentInventoryItem::with(['productVariant.product', 'inventorySource']);

        if ($query->search !== '') {
            $search = $query->search;
            $builder->where(function ($q) use ($search): void {
                $q->whereHas('productVariant', fn ($v) => $v->where('sku', 'like', "%{$search}%"))
                  ->orWhereHas('productVariant.product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($query->source_id !== '') {
            $builder->where('source_id', $query->source_id);
        }

        if ($query->low_stock_only) {
            $builder->whereRaw('qty_available - qty_reserved <= low_stock_threshold');
        }

        return $builder->paginate($query->perPage)
            ->through(fn ($item) => InventoryItemData::fromModel($item));
    }
}
