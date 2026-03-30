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

final readonly class CreateBrandCommand
{
    public function __construct(
        public string  $name,
        public string  $slug,
        public ?string $description = null,
        public bool    $is_active   = true,
    ) {}
}
