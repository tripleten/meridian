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
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxZone;

final readonly class UpdateTaxZoneHandler
{
    public function handle(UpdateTaxZoneCommand $command): void
    {
        /** @var EloquentTaxZone $taxZone */
        $taxZone = EloquentTaxZone::findOrFail($command->id);

        $duplicate = EloquentTaxZone::where('code', $command->code)
            ->where('id', '!=', $command->id)
            ->exists();

        if ($duplicate) {
            throw new DomainException("A tax zone with code '{$command->code}' already exists.");
        }

        $taxZone->name      = $command->name;
        $taxZone->code      = $command->code;
        $taxZone->countries = $command->countries;
        $taxZone->regions   = $command->regions;
        $taxZone->save();
    }
}
