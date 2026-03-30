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

use Meridian\CmsSeo\Infrastructure\Persistence\EloquentCmsPage;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class GetCmsPageHandler
{
    public function handle(GetCmsPageQuery $query): EloquentCmsPage
    {
        $page = EloquentCmsPage::find($query->pageId);

        if ($page === null) {
            throw new DomainException("CMS page '{$query->pageId}' not found.");
        }

        return $page;
    }
}
