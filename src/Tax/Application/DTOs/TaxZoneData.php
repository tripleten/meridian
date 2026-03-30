<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Tax\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Tax\Application\DTOs;

use Spatie\LaravelData\Data;

final class TaxZoneData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $code,
        public readonly array   $countries,
        public readonly ?array  $regions,
        public readonly int     $rate_count,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:         $model->id,
            name:       $model->name,
            code:       $model->code,
            countries:  $model->countries ?? [],
            regions:    $model->regions,
            rate_count: (int) ($model->tax_rates_count ?? 0),
            created_at: $model->created_at?->toIso8601String() ?? '',
        );
    }
}
