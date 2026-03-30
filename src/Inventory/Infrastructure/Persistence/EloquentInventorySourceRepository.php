<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Infrastructure\Persistence;

use Meridian\Inventory\Domain\Repositories\InventorySourceRepositoryInterface;

class EloquentInventorySourceRepository implements InventorySourceRepositoryInterface
{
    public function findById(string $id): ?object
    {
        return EloquentInventorySource::find($id);
    }

    public function findByCode(string $code): ?object
    {
        return EloquentInventorySource::where('code', $code)->first();
    }

    public function all(): array
    {
        return EloquentInventorySource::orderBy('priority', 'asc')->get()->all();
    }

    public function save(object $source): void
    {
        /** @var EloquentInventorySource $source */
        $source->save();
    }
}
