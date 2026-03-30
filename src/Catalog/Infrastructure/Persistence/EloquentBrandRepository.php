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

use Meridian\Catalog\Domain\Repositories\BrandRepositoryInterface;

final class EloquentBrandRepository implements BrandRepositoryInterface
{
    public function findById(string $id): ?EloquentBrand
    {
        return EloquentBrand::find($id);
    }

    public function all(): mixed
    {
        return EloquentBrand::where('is_active', true)->orderBy('name')->get();
    }

    public function paginate(string $search = '', int $perPage = 20): mixed
    {
        return EloquentBrand::withCount('products')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function save(object $brand): void
    {
        $brand->save();
    }

    public function delete(string $id): void
    {
        EloquentBrand::find($id)?->delete();
    }
}
