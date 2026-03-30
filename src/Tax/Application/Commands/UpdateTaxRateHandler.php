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

use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxRate;

final readonly class UpdateTaxRateHandler
{
    public function handle(UpdateTaxRateCommand $command): void
    {
        /** @var EloquentTaxRate $taxRate */
        $taxRate = EloquentTaxRate::findOrFail($command->id);

        $duplicate = EloquentTaxRate::where('code', $command->code)
            ->where('id', '!=', $command->id)
            ->exists();

        if ($duplicate) {
            throw new DomainException("A tax rate with code '{$command->code}' already exists.");
        }

        $taxRate->name                = $command->name;
        $taxRate->code                = $command->code;
        $taxRate->rate                = $command->rate;
        $taxRate->type                = $command->type;
        $taxRate->is_compound         = $command->is_compound;
        $taxRate->is_shipping_taxable = $command->is_shipping_taxable;
        $taxRate->save();
    }
}
