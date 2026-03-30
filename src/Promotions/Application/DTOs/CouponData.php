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

use Meridian\Promotions\Domain\CouponType;
use Spatie\LaravelData\Data;

final class CouponData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $code,
        public readonly ?string $description,
        public readonly string  $type,
        public readonly string  $type_label,
        public readonly ?int    $usage_limit,
        public readonly ?int    $usage_limit_per_customer,
        public readonly int     $times_used,
        public readonly ?string $cart_rule_id,
        public readonly bool    $is_active,
        public readonly ?string $valid_from,
        public readonly ?string $valid_until,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        $type = $model->type instanceof CouponType
            ? $model->type
            : CouponType::from((string) $model->type);

        return new self(
            id:                       (string) $model->id,
            code:                     $model->code,
            description:              $model->description,
            type:                     $type->value,
            type_label:               $type->label(),
            usage_limit:              $model->usage_limit !== null ? (int) $model->usage_limit : null,
            usage_limit_per_customer: $model->usage_limit_per_customer !== null ? (int) $model->usage_limit_per_customer : null,
            times_used:               (int) $model->times_used,
            cart_rule_id:             $model->cart_rule_id ? (string) $model->cart_rule_id : null,
            is_active:                (bool) $model->is_active,
            valid_from:               $model->valid_from?->toIso8601String(),
            valid_until:              $model->valid_until?->toIso8601String(),
            created_at:               $model->created_at->toIso8601String(),
        );
    }
}
