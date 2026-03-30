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

final readonly class CreateTaxRuleCommand
{
    public function __construct(
        public string $name,
        public int    $priority,
        public array  $tax_class_ids,
        public array  $tax_zone_ids,
        public array  $tax_rate_ids,
        public bool   $is_active,
    ) {}
}
