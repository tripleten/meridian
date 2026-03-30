<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Domain\Product
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Domain\Product;

enum Visibility: string
{
    case CatalogSearch = 'catalog_search'; // shows in catalog + search (default)
    case Catalog       = 'catalog';        // catalog only
    case Search        = 'search';         // search only
    case Hidden        = 'hidden';         // not visible (variants, etc.)

    public function label(): string
    {
        return match ($this) {
            self::CatalogSearch => 'Catalog & Search',
            self::Catalog       => 'Catalog only',
            self::Search        => 'Search only',
            self::Hidden        => 'Hidden',
        };
    }
}
