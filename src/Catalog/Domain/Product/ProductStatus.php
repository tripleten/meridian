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

enum ProductStatus: string
{
    case Draft    = 'draft';
    case Active   = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft    => 'Draft',
            self::Active   => 'Active',
            self::Archived => 'Archived',
        };
    }

    public function isVisible(): bool
    {
        return $this === self::Active;
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft    => [self::Active],
            self::Active   => [self::Archived],
            self::Archived => [self::Draft],
        };
    }

    public function canTransitionTo(self $new): bool
    {
        return in_array($new, $this->allowedTransitions(), true);
    }
}
