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

final readonly class CreateCmsPageCommand
{
    public function __construct(
        public string  $title,
        public string  $url_key,
        public ?string $content,
        public string  $state                = 'draft',
        public ?string $meta_title           = null,
        public ?string $meta_description     = null,
        public ?string $meta_keywords        = null,
        public bool    $meta_robots_noindex  = false,
        public ?string $channel_id           = null,
    ) {}
}
