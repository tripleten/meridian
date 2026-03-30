<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Settings\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Settings\Application\DTOs;

use Spatie\LaravelData\Data;

class SettingGroupData extends Data
{
    public function __construct(
        public readonly string  $group,
        public readonly string  $label,
        public readonly array   $settings,  // key => value
    ) {}
}
