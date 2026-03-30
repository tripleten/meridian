<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Infrastructure\Persistence;

use Meridian\Pricing\Domain\Repositories\PriceListRepositoryInterface;

final class EloquentPriceListRepository implements PriceListRepositoryInterface
{
    public function findById(string $id): ?EloquentPriceList
    {
        return EloquentPriceList::find($id);
    }

    public function findByCode(string $code): ?EloquentPriceList
    {
        return EloquentPriceList::where('code', $code)->first();
    }

    public function all(bool $activeOnly = false): array
    {
        $query = EloquentPriceList::orderBy('name');
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        return $query->get()->all();
    }

    public function save(object $priceList): void
    {
        $priceList->save();
    }

    public function delete(string $id): void
    {
        EloquentPriceList::find($id)?->delete();
    }
}
