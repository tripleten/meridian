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

final class TaxRuleData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int    $priority,
        public readonly array  $tax_class_ids,
        public readonly array  $tax_zone_ids,
        public readonly array  $tax_rate_ids,
        public readonly bool   $is_active,
        public readonly string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:            $model->id,
            name:          $model->name,
            priority:      (int) $model->priority,
            tax_class_ids: $model->tax_class_ids ?? [],
            tax_zone_ids:  $model->tax_zone_ids ?? [],
            tax_rate_ids:  $model->tax_rate_ids ?? [],
            is_active:     (bool) $model->is_active,
            created_at:    $model->created_at?->toIso8601String() ?? '',
        );
    }
}
