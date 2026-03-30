<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\Commands;

final readonly class CreateInventorySourceCommand
{
    public function __construct(
        public string  $name,
        public string  $code,
        public string  $type          = 'warehouse',
        public ?string $address_line1 = null,
        public ?string $city          = null,
        public ?string $country_code  = null,
        public bool    $is_active     = true,
        public bool    $is_default    = false,
        public int     $priority      = 0,
    ) {}
}
