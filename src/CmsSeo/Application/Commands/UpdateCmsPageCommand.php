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

final readonly class UpdateCmsPageCommand
{
    public function __construct(
        public string  $pageId,
        public string  $title,
        public string  $url_key,
        public ?string $content,
        public string  $state,
        public ?string $meta_title,
        public ?string $meta_description,
        public ?string $meta_keywords,
        public bool    $meta_robots_noindex,
        public ?string $channel_id,
    ) {}
}
