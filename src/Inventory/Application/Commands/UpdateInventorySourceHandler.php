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

final class UpdateInventorySourceHandler
{
    public function handle(UpdateInventorySourceCommand $command): EloquentInventorySource
    {
        /** @var EloquentInventorySource $source */
        $source = EloquentInventorySource::find($command->sourceId);

        if ($source === null) {
            throw new DomainException("Inventory source '{$command->sourceId}' not found.");
        }

        if ($command->is_default && ! $source->is_default) {
            EloquentInventorySource::where('is_default', true)->update(['is_default' => false]);
        }

        $source->name          = $command->name;
        $source->code          = $command->code;
        $source->type          = $command->type;
        $source->address_line1 = $command->address_line1;
        $source->city          = $command->city;
        $source->country_code  = $command->country_code;
        $source->is_active     = $command->is_active;
        $source->is_default    = $command->is_default;
        $source->priority      = $command->priority;
        $source->save();

        return $source;
    }
}
