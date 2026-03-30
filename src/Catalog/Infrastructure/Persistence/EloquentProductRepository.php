<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Infrastructure\Persistence;

use Meridian\Catalog\Domain\Repositories\ProductRepositoryInterface;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(string $id): ?EloquentProduct
    {
        return EloquentProduct::with('brand')->find($id);
    }

    public function findBySlug(string $slug): ?EloquentProduct
    {
        return EloquentProduct::with('brand')->where('slug', $slug)->first();
    }

    public function findBySku(string $sku): ?EloquentProduct
    {
        return EloquentProduct::where('sku', $sku)->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): mixed
    {
        $query = EloquentProduct::with('brand')->orderByDesc('created_at');

        if (! empty($filters['search'])) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$filters['search']}%")
                ->orWhere('sku', 'like', "%{$filters['search']}%")
            );
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        return $query->paginate($perPage);
    }

    public function save(object $product): void
    {
        $product->save();
    }

    public function delete(string $id): void
    {
        EloquentProduct::find($id)?->delete();
    }
}
