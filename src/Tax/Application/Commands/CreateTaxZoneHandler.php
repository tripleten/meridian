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
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxZone;

final readonly class CreateTaxZoneHandler
{
    public function handle(CreateTaxZoneCommand $command): void
    {
        $exists = EloquentTaxZone::where('code', $command->code)->exists();

        if ($exists) {
            throw new DomainException("A tax zone with code '{$command->code}' already exists.");
        }

        EloquentTaxZone::create([
            'id'        => (string) Str::ulid(),
            'name'      => $command->name,
            'code'      => $command->code,
            'countries' => $command->countries,
            'regions'   => $command->regions,
        ]);
    }
}
