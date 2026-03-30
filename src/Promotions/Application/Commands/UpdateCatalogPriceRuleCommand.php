<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Promotions\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Promotions\Application\Commands;

final class UpdateCatalogPriceRuleCommand
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly string  $discount_type,
        public readonly float   $discount_amount,
        public readonly bool    $is_active,
        public readonly int     $priority,
        public readonly bool    $stop_further_rules,
        public readonly ?array  $category_ids,
        public readonly ?string $valid_from,
        public readonly ?string $valid_until,
    ) {}
}
