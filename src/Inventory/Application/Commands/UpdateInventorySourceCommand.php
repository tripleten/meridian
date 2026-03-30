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

final readonly class UpdateInventorySourceCommand
{
    public function __construct(
        public string  $sourceId,
        public string  $name,
        public string  $code,
        public string  $type,
        public ?string $address_line1,
        public ?string $city,
        public ?string $country_code,
        public bool    $is_active,
        public bool    $is_default,
        public int     $priority,
    ) {}
}
