<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Tax\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Tax\Application\Commands;

final readonly class CreateTaxZoneCommand
{
    public function __construct(
        public string  $name,
        public string  $code,
        public array   $countries,
        public ?array  $regions = null,
    ) {}
}
