<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\Queries;

use DomainException;
use Meridian\Catalog\Application\DTOs\ProductData;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProduct;

final readonly class GetProductHandler
{
    public function handle(GetProductQuery $query): ProductData
    {
        $product = EloquentProduct::with(['brand'])->find($query->productId);

        if ($product === null) {
            throw new DomainException("Product '{$query->productId}' not found.");
        }

        return ProductData::fromModel($product);
    }
}
