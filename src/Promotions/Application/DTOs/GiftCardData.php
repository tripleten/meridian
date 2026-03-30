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

use Meridian\Promotions\Domain\GiftCardState;
use Spatie\LaravelData\Data;

final class GiftCardData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $code,
        public readonly string  $state,
        public readonly string  $state_label,
        public readonly string  $state_badge,
        public readonly int     $initial_balance,
        public readonly int     $remaining_balance,
        public readonly string  $currency_code,
        public readonly ?string $customer_id,
        public readonly ?string $expires_at,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        $state = $model->state instanceof GiftCardState
            ? $model->state
            : GiftCardState::from((string) $model->state);

        return new self(
            id:                (string) $model->id,
            code:              $model->code,
            state:             $state->value,
            state_label:       $state->label(),
            state_badge:       $state->badgeVariant(),
            initial_balance:   (int) $model->initial_balance,
            remaining_balance: (int) $model->remaining_balance,
            currency_code:     $model->currency_code,
            customer_id:       $model->customer_id ? (string) $model->customer_id : null,
            expires_at:        $model->expires_at?->toIso8601String(),
            created_at:        $model->created_at->toIso8601String(),
        );
    }
}
