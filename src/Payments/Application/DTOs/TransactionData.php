<?php

declare(strict_types=1);

namespace Meridian\Payments\Application\DTOs;

use Meridian\Payments\Domain\TransactionState;
use Meridian\Payments\Domain\TransactionType;
use Spatie\LaravelData\Data;

class TransactionData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $order_id,
        public readonly string  $type,
        public readonly string  $type_label,
        public readonly string  $state,
        public readonly string  $state_label,
        public readonly int     $amount,
        public readonly string  $currency_code,
        public readonly ?string $gateway_transaction_id,
        public readonly ?string $gateway_response,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        $type  = $model->type instanceof TransactionType
            ? $model->type
            : TransactionType::from($model->type);

        $state = $model->state instanceof TransactionState
            ? $model->state
            : TransactionState::from($model->state);

        return new self(
            id:                     $model->id,
            order_id:               $model->order_id,
            type:                   $type->value,
            type_label:             $type->label(),
            state:                  $state->value,
            state_label:            $state->label(),
            amount:                 (int) $model->amount,
            currency_code:          $model->currency_code,
            gateway_transaction_id: $model->gateway_transaction_id,
            gateway_response:       is_array($model->gateway_response)
                ? json_encode($model->gateway_response)
                : $model->gateway_response,
            created_at:             $model->created_at?->toIso8601String() ?? '',
        );
    }
}
