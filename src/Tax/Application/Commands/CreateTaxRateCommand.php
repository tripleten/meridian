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

final readonly class CreateTaxRateCommand
{
    public function __construct(
        public string $tax_zone_id,
        public string $name,
        public string $code,
        public float  $rate,
        public string $type,
        public bool   $is_compound,
        public bool   $is_shipping_taxable,
    ) {}
}
