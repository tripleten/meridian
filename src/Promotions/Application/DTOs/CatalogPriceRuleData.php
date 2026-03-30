<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Promotions\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Promotions\Application\DTOs;

use Meridian\Promotions\Domain\CatalogDiscountType;
use Spatie\LaravelData\Data;

final class CatalogPriceRuleData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly string  $discount_type,
        public readonly string  $discount_type_label,
        public readonly float   $discount_amount,
        public readonly bool    $is_active,
        public readonly int     $priority,
        public readonly ?string $valid_from,
        public readonly ?string $valid_until,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        $discountType = $model->discount_type instanceof CatalogDiscountType
            ? $model->discount_type
            : CatalogDiscountType::from((string) $model->discount_type);

        return new self(
            id:                  (string) $model->id,
            name:                $model->name,
            description:         $model->description,
            discount_type:       $discountType->value,
            discount_type_label: $discountType->label(),
            discount_amount:     (float) $model->discount_amount,
            is_active:           (bool) $model->is_active,
            priority:            (int) $model->priority,
            valid_from:          $model->valid_from?->toIso8601String(),
            valid_until:         $model->valid_until?->toIso8601String(),
            created_at:          $model->created_at->toIso8601String(),
        );
    }
}
