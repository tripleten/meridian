<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\DTOs;

use Spatie\LaravelData\Data;

final class ProductVariantData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $product_id,
        public readonly string  $sku,
        public readonly ?string $name,
        public readonly ?int    $price,
        public readonly ?int    $compare_price,
        public readonly ?int    $cost_price,
        public readonly ?float  $weight,
        public readonly bool    $is_active,
        public readonly int     $sort_order,
    ) {}

    public static function fromModel(object $variant): self
    {
        return new self(
            id:            (string) $variant->id,
            product_id:    (string) $variant->product_id,
            sku:           $variant->sku,
            name:          $variant->name,
            price:         $variant->price !== null ? (int) $variant->price : null,
            compare_price: $variant->compare_price !== null ? (int) $variant->compare_price : null,
            cost_price:    $variant->cost_price !== null ? (int) $variant->cost_price : null,
            weight:        $variant->weight !== null ? (float) $variant->weight : null,
            is_active:     (bool) $variant->is_active,
            sort_order:    (int) $variant->sort_order,
        );
    }
}
