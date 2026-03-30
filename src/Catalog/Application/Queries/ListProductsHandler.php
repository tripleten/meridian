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

use Illuminate\Pagination\LengthAwarePaginator;
use Meridian\Catalog\Application\DTOs\ProductData;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProduct;

final readonly class ListProductsHandler
{
    public function handle(ListProductsQuery $query): LengthAwarePaginator
    {
        $builder = EloquentProduct::with(['brand']);

        if ($query->search !== '') {
            $term = '%' . $query->search . '%';
            $builder->where(function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                  ->orWhere('sku', 'like', $term);
            });
        }

        if ($query->status !== '') {
            $builder->where('status', $query->status);
        }

        if ($query->type !== '') {
            $builder->where('type', $query->type);
        }

        if ($query->brand_id !== null) {
            $builder->where('brand_id', $query->brand_id);
        }

        $paginator = $builder
            ->orderBy('created_at', 'desc')
            ->paginate($query->perPage);

        $paginator->through(fn (object $product) => ProductData::fromModel($product));

        return $paginator;
    }
}
