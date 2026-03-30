<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Application\DTOs;

use Spatie\LaravelData\Data;

class OrderItemData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $sku,
        public readonly string $name,
        public readonly int    $quantity,
        public readonly int    $unit_price,
        public readonly int    $unit_price_incl_tax,
        public readonly int    $row_total,
        public readonly int    $row_total_incl_tax,
        public readonly int    $discount_amount,
        public readonly int    $tax_amount,
        public readonly string $tax_rate,
        public readonly int    $quantity_refunded,
        public readonly int    $refunded_amount,
    ) {}

    public static function fromModel(object $item): self
    {
        return new self(
            id:                  $item->id,
            sku:                 $item->sku,
            name:                $item->name,
            quantity:            (int) $item->quantity,
            unit_price:          (int) $item->unit_price,
            unit_price_incl_tax: (int) $item->unit_price_incl_tax,
            row_total:           (int) $item->row_total,
            row_total_incl_tax:  (int) $item->row_total_incl_tax,
            discount_amount:     (int) $item->discount_amount,
            tax_amount:          (int) $item->tax_amount,
            tax_rate:            number_format((float) $item->tax_rate * 100, 0) . '%',
            quantity_refunded:   (int) $item->quantity_refunded,
            refunded_amount:     (int) $item->refunded_amount,
        );
    }
}
