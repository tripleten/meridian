<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Application\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Meridian\CmsSeo\Application\DTOs\CmsPageData;
use Meridian\CmsSeo\Infrastructure\Persistence\EloquentCmsPage;

final readonly class ListCmsPagesHandler
{
    public function handle(ListCmsPagesQuery $query): LengthAwarePaginator
    {
        $builder = EloquentCmsPage::query();

        if ($query->search !== '') {
            $builder->where('title', 'like', "%{$query->search}%");
        }

        if ($query->state !== '') {
            $builder->where('state', $query->state);
        }

        return $builder
            ->orderBy('created_at', 'desc')
            ->paginate($query->perPage)
            ->through(fn ($page) => CmsPageData::fromModel($page));
    }
}
