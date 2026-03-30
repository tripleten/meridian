<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\Queries;

use Meridian\Inventory\Application\DTOs\InventorySourceData;
use Meridian\Inventory\Infrastructure\Persistence\EloquentInventorySource;

final class ListInventorySourcesHandler
{
    /**
     * @return InventorySourceData[]
     */
    public function handle(): array
    {
        return EloquentInventorySource::orderBy('priority', 'asc')
            ->get()
            ->map(fn ($source) => InventorySourceData::fromModel($source))
            ->all();
    }
}
