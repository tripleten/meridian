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

final class ProductData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $sku,
        public readonly string  $type,
        public readonly string  $status,
        public readonly string  $visibility,
        public readonly int     $base_price,
        public readonly ?int    $compare_price,
        public readonly ?int    $cost_price,
        public readonly ?string $brand_id,
        public readonly ?string $brand_name,
        public readonly ?string $attribute_set_id,
        public readonly ?string $tax_class_id,
        public readonly string  $url_key,
        public readonly ?string $short_description,
        public readonly ?string $description,
        public readonly ?float  $weight,
        public readonly string  $weight_unit,
        public readonly bool    $is_in_stock,
        public readonly bool    $is_featured,
        public readonly bool    $is_purchasable,
        public readonly ?string $meta_title,
        public readonly ?string $meta_description,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $product): self
    {
        return new self(
            id:                (string) $product->id,
            name:              $product->name,
            sku:               $product->sku,
            type:              $product->type instanceof \BackedEnum ? $product->type->value : (string) $product->type,
            status:            $product->status instanceof \BackedEnum ? $product->status->value : (string) $product->status,
            visibility:        $product->visibility instanceof \BackedEnum ? $product->visibility->value : (string) $product->visibility,
            base_price:        (int) $product->base_price,
            compare_price:     $product->compare_price !== null ? (int) $product->compare_price : null,
            cost_price:        $product->cost_price !== null ? (int) $product->cost_price : null,
            brand_id:          $product->brand_id ? (string) $product->brand_id : null,
            brand_name:        $product->brand?->name,
            attribute_set_id:  $product->attribute_set_id ? (string) $product->attribute_set_id : null,
            tax_class_id:      $product->tax_class_id ? (string) $product->tax_class_id : null,
            url_key:           $product->url_key,
            short_description: $product->short_description,
            description:       $product->description,
            weight:            $product->weight !== null ? (float) $product->weight : null,
            weight_unit:       $product->weight_unit ?? 'kg',
            is_in_stock:       (bool) $product->is_in_stock,
            is_featured:       (bool) $product->is_featured,
            is_purchasable:    (bool) $product->is_purchasable,
            meta_title:        $product->seo_title,
            meta_description:  $product->seo_description,
            created_at:        $product->created_at->toDateTimeString(),
        );
    }
}
