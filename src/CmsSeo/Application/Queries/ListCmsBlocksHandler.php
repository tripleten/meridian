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

use Meridian\CmsSeo\Application\DTOs\CmsBlockData;
use Meridian\CmsSeo\Infrastructure\Persistence\EloquentCmsBlock;

final readonly class ListCmsBlocksHandler
{
    public function handle(): array
    {
        return EloquentCmsBlock::orderBy('title')
            ->get()
            ->map(fn ($block) => CmsBlockData::fromModel($block))
            ->values()
            ->all();
    }
}
