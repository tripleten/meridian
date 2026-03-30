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

final readonly class CreateCmsBlockCommand
{
    public function __construct(
        public string  $identifier,
        public string  $title,
        public ?string $content,
        public bool    $is_active  = true,
        public ?string $channel_id = null,
    ) {}
}
