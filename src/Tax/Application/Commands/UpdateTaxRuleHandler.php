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

use Meridian\Tax\Infrastructure\Persistence\EloquentTaxRule;

final readonly class UpdateTaxRuleHandler
{
    public function handle(UpdateTaxRuleCommand $command): void
    {
        /** @var EloquentTaxRule $taxRule */
        $taxRule = EloquentTaxRule::findOrFail($command->id);

        $taxRule->name          = $command->name;
        $taxRule->priority      = $command->priority;
        $taxRule->tax_class_ids = $command->tax_class_ids;
        $taxRule->tax_zone_ids  = $command->tax_zone_ids;
        $taxRule->tax_rate_ids  = $command->tax_rate_ids;
        $taxRule->is_active     = $command->is_active;
        $taxRule->save();
    }
}
