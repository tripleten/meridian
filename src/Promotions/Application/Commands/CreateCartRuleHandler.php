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

use Meridian\Promotions\Infrastructure\Persistence\EloquentCartRule;

final class CreateCartRuleHandler
{
    public function handle(CreateCartRuleCommand $command): EloquentCartRule
    {
        $rule = new EloquentCartRule();
        $rule->name                  = $command->name;
        $rule->description           = $command->description;
        $rule->discount_type         = $command->discount_type;
        $rule->discount_amount       = $command->discount_amount;
        $rule->discount_qty          = $command->discount_qty;
        $rule->apply_to_shipping     = $command->apply_to_shipping;
        $rule->stop_rules_processing = $command->stop_rules_processing;
        $rule->conditions            = $command->conditions;
        $rule->is_active             = $command->is_active;
        $rule->valid_from            = $command->valid_from;
        $rule->valid_until           = $command->valid_until;
        $rule->uses_per_coupon       = $command->uses_per_coupon;
        $rule->uses_per_customer     = $command->uses_per_customer;
        $rule->sort_order            = $command->sort_order;
        $rule->save();

        return $rule;
    }
}
