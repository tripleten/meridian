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

final class TaxRateData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $tax_zone_id,
        public readonly ?string $tax_zone_name,
        public readonly string  $name,
        public readonly string  $code,
        public readonly float   $rate,
        public readonly string  $type,
        public readonly bool    $is_compound,
        public readonly bool    $is_shipping_taxable,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:                  $model->id,
            tax_zone_id:         $model->tax_zone_id,
            tax_zone_name:       $model->taxZone?->name,
            name:                $model->name,
            code:                $model->code,
            rate:                (float) $model->rate,
            type:                $model->type,
            is_compound:         (bool) $model->is_compound,
            is_shipping_taxable: (bool) $model->is_shipping_taxable,
            created_at:          $model->created_at?->toIso8601String() ?? '',
        );
    }
}
