<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Application\DTOs;

use Spatie\LaravelData\Data;

final class CustomerGroupData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $code,
        public readonly bool   $is_default,
        public readonly int    $customer_count,
    ) {}

    public static function fromModel(object $group): self
    {
        return new self(
            id:             $group->id,
            name:           $group->name,
            code:           $group->code,
            is_default:     (bool) $group->is_default,
            customer_count: $group->customers_count ?? 0,
        );
    }
}
