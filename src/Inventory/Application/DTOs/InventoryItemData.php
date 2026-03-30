<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\DTOs;

use Spatie\LaravelData\Data;

class InventoryItemData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $product_variant_id,
        public readonly string $source_id,
        public readonly string $source_name,
        public readonly string $variant_sku,
        public readonly string $product_name,
        public readonly int    $qty_available,
        public readonly int    $qty_reserved,
        public readonly int    $qty_incoming,
        public readonly int    $qty_saleable,
        public readonly int    $low_stock_threshold,
        public readonly bool   $backorders_allowed,
        public readonly bool   $manage_stock,
    ) {}

    public static function fromModel(object $item): self
    {
        $qtyAvailable = (int) $item->qty_available;
        $qtyReserved  = (int) $item->qty_reserved;

        return new self(
            id:                  $item->id,
            product_variant_id:  $item->product_variant_id,
            source_id:           $item->source_id,
            source_name:         $item->inventorySource->name,
            variant_sku:         $item->productVariant->sku,
            product_name:        $item->productVariant->product->name,
            qty_available:       $qtyAvailable,
            qty_reserved:        $qtyReserved,
            qty_incoming:        (int) $item->qty_incoming,
            qty_saleable:        max(0, $qtyAvailable - $qtyReserved),
            low_stock_threshold: (int) $item->low_stock_threshold,
            backorders_allowed:  (bool) $item->backorders_allowed,
            manage_stock:        (bool) $item->manage_stock,
        );
    }
}
