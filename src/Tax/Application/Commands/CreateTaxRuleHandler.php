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

use Illuminate\Support\Str;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxRule;

final readonly class CreateTaxRuleHandler
{
    public function handle(CreateTaxRuleCommand $command): void
    {
        EloquentTaxRule::create([
            'id'            => (string) Str::ulid(),
            'name'          => $command->name,
            'priority'      => $command->priority,
            'tax_class_ids' => $command->tax_class_ids,
            'tax_zone_ids'  => $command->tax_zone_ids,
            'tax_rate_ids'  => $command->tax_rate_ids,
            'is_active'     => $command->is_active,
        ]);
    }
}
