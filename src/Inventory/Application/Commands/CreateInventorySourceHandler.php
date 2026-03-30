<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\Commands;

use Meridian\Inventory\Infrastructure\Persistence\EloquentInventorySource;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Illuminate\Support\Str;

final class CreateInventorySourceHandler
{
    public function handle(CreateInventorySourceCommand $command): EloquentInventorySource
    {
        if (EloquentInventorySource::where('code', $command->code)->exists()) {
            throw new DomainException("Inventory source code '{$command->code}' is already taken.");
        }

        if ($command->is_default) {
            EloquentInventorySource::where('is_default', true)->update(['is_default' => false]);
        }

        return EloquentInventorySource::create([
            'id'           => (string) Str::ulid(),
            'name'         => $command->name,
            'code'         => $command->code,
            'type'         => $command->type,
            'address_line1'=> $command->address_line1,
            'city'         => $command->city,
            'country_code' => $command->country_code,
            'is_active'    => $command->is_active,
            'is_default'   => $command->is_default,
            'priority'     => $command->priority,
        ]);
    }
}
