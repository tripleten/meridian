<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Domain\Customer
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Domain\Customer;

enum CustomerGender: string
{
    case Male            = 'male';
    case Female          = 'female';
    case PreferNotToSay  = 'prefer_not_to_say';

    public function label(): string
    {
        return match ($this) {
            self::Male           => 'Male',
            self::Female         => 'Female',
            self::PreferNotToSay => 'Prefer not to say',
        };
    }
}
