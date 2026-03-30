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
use Meridian\Catalog\Application\DTOs\BrandData;
use Meridian\Catalog\Infrastructure\Persistence\EloquentBrand;

final readonly class ListBrandsHandler
{
    public function handle(ListBrandsQuery $query): LengthAwarePaginator
    {
        $builder = EloquentBrand::withCount('products');

        if ($query->search !== '') {
            $builder->where('name', 'like', '%' . $query->search . '%');
        }

        $paginator = $builder
            ->orderBy('name')
            ->paginate($query->perPage);

        $paginator->through(fn (object $brand) => BrandData::fromModel($brand));

        return $paginator;
    }
}
