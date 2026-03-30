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

use Meridian\Catalog\Domain\Repositories\CategoryRepositoryInterface;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function findById(string $id): ?EloquentCategory
    {
        return EloquentCategory::find($id);
    }

    public function all(): mixed
    {
        return EloquentCategory::orderBy('_lft')->get();
    }

    public function save(object $category): void
    {
        $category->save();
    }

    public function delete(string $id): void
    {
        EloquentCategory::find($id)?->delete();
    }
}
