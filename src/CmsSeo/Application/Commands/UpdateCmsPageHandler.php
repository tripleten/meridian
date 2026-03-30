<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Application\Commands;

use Meridian\CmsSeo\Domain\Repositories\CmsPageRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class UpdateCmsPageHandler
{
    public function __construct(
        private CmsPageRepositoryInterface $pages,
    ) {}

    public function handle(UpdateCmsPageCommand $command): void
    {
        $page = $this->pages->findById($command->pageId);

        if ($page === null) {
            throw new DomainException("CMS page '{$command->pageId}' not found.");
        }

        // If transitioning to published and no published_at yet, set it now
        if ($command->state === 'published' && $page->published_at === null) {
            $page->published_at = now();
        }

        $page->channel_id          = $command->channel_id;
        $page->title               = $command->title;
        $page->url_key             = $command->url_key;
        $page->content             = $command->content;
        $page->state               = $command->state;
        $page->meta_title          = $command->meta_title;
        $page->meta_description    = $command->meta_description;
        $page->meta_keywords       = $command->meta_keywords;
        $page->meta_robots_noindex = $command->meta_robots_noindex;

        $this->pages->save($page);
    }
}
