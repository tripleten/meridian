<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\Commands;

use DomainException;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProductVariant;

final readonly class CreateProductVariantHandler
{
    public function handle(CreateProductVariantCommand $command): void
    {
        if (EloquentProductVariant::where('sku', $command->sku)->exists()) {
            throw new DomainException("A variant with SKU '{$command->sku}' already exists.");
        }

        EloquentProductVariant::create([
            'product_id'    => $command->productId,
            'sku'           => $command->sku,
            'name'          => $command->name,
            'price'         => $command->price,
            'compare_price' => $command->compare_price,
            'cost_price'    => $command->cost_price,
            'weight'        => $command->weight,
            'is_active'     => $command->is_active,
            'sort_order'    => $command->sort_order,
        ]);
    }
}
