<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Application\DTOs;

use Spatie\LaravelData\Data;

class InventorySourceData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $code,
        public readonly string  $type,
        public readonly ?string $address_line1,
        public readonly ?string $city,
        public readonly ?string $country_code,
        public readonly bool    $is_active,
        public readonly bool    $is_default,
        public readonly int     $priority,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $source): self
    {
        return new self(
            id:           $source->id,
            name:         $source->name,
            code:         $source->code,
            type:         $source->type instanceof \BackedEnum ? $source->type->value : (string) $source->type,
            address_line1: $source->address_line1,
            city:         $source->city,
            country_code: $source->country_code,
            is_active:    (bool) $source->is_active,
            is_default:   (bool) $source->is_default,
            priority:     (int) $source->priority,
            created_at:   $source->created_at?->toDateTimeString() ?? '',
        );
    }
}
