<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\Commands;

final readonly class UpdateCategoryCommand
{
    public function __construct(
        public string  $categoryId,
        public string  $name,
        public string  $url_key,
        public ?string $parent_id        = null,
        public ?string $description      = null,
        public bool    $is_active        = true,
        public ?string $meta_title       = null,
        public ?string $meta_description = null,
    ) {}
}
