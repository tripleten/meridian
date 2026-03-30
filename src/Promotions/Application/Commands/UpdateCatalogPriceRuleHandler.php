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

use Meridian\Promotions\Infrastructure\Persistence\EloquentCatalogPriceRule;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class UpdateCatalogPriceRuleHandler
{
    public function handle(UpdateCatalogPriceRuleCommand $command): EloquentCatalogPriceRule
    {
        $rule = EloquentCatalogPriceRule::find($command->id);

        if ($rule === null) {
            throw new DomainException("Catalog price rule '{$command->id}' not found.");
        }

        $rule->name               = $command->name;
        $rule->description        = $command->description;
        $rule->discount_type      = $command->discount_type;
        $rule->discount_amount    = $command->discount_amount;
        $rule->is_active          = $command->is_active;
        $rule->priority           = $command->priority;
        $rule->stop_further_rules = $command->stop_further_rules;
        $rule->category_ids       = $command->category_ids;
        $rule->valid_from         = $command->valid_from;
        $rule->valid_until        = $command->valid_until;
        $rule->save();

        return $rule;
    }
}
