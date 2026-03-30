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
    case Visible = 'visible'; // shows in catalog + search
    case Hidden  = 'hidden';  // variants — never get their own URL

    public function label(): string
    {
        return match ($this) {
            self::Visible => 'Visible',
            self::Hidden  => 'Hidden',
        };
    }
}
