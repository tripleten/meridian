<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Application\Queries;

use Meridian\Pricing\Application\DTOs\PriceListData;
use Meridian\Pricing\Infrastructure\Persistence\EloquentPriceList;

final readonly class ListPriceListsHandler
{
    public function handle(): array
    {
        return EloquentPriceList::withCount('priceListItems')
            ->with('customerGroup')
            ->orderBy('name')
            ->get()
            ->map(fn (object $pl) => PriceListData::fromModel($pl))
            ->all();
    }
}
