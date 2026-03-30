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

final class BrandData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $slug,
        public readonly ?string $description,
        public readonly bool    $is_active,
        public readonly int     $product_count,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $brand): self
    {
        return new self(
            id:            (string) $brand->id,
            name:          $brand->name,
            slug:          $brand->slug,
            description:   $brand->description,
            is_active:     (bool) $brand->is_active,
            product_count: (int) ($brand->products_count ?? 0),
            created_at:    $brand->created_at->toDateTimeString(),
        );
    }
}
