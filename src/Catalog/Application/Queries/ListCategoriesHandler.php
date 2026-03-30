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

use Meridian\Catalog\Application\DTOs\CategoryData;
use Meridian\Catalog\Infrastructure\Persistence\EloquentCategory;

final readonly class ListCategoriesHandler
{
    /**
     * @return CategoryData[]
     */
    public function handle(): array
    {
        return EloquentCategory::orderBy('_lft')
            ->get()
            ->map(fn (object $category) => CategoryData::fromModel($category))
            ->all();
    }
}
