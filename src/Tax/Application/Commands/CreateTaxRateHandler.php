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
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxRate;

final readonly class CreateTaxRateHandler
{
    public function handle(CreateTaxRateCommand $command): void
    {
        $exists = EloquentTaxRate::where('code', $command->code)->exists();

        if ($exists) {
            throw new DomainException("A tax rate with code '{$command->code}' already exists.");
        }

        EloquentTaxRate::create([
            'id'                  => (string) Str::ulid(),
            'tax_zone_id'         => $command->tax_zone_id,
            'name'                => $command->name,
            'code'                => $command->code,
            'rate'                => $command->rate,
            'type'                => $command->type,
            'is_compound'         => $command->is_compound,
            'is_shipping_taxable' => $command->is_shipping_taxable,
        ]);
    }
}
