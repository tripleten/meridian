<?php

declare(strict_types=1);

namespace Meridian\Payments\Application\DTOs;

use Spatie\LaravelData\Data;

class PaymentMethodData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $code,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly bool    $is_active,
        public readonly int     $sort_order,
        public readonly array   $config,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:          $model->id,
            code:        $model->code,
            name:        $model->name,
            description: $model->description,
            is_active:   (bool) $model->is_active,
            sort_order:  (int) $model->sort_order,
            config:      $model->config ?? [],
            created_at:  $model->created_at?->toIso8601String() ?? '',
        );
    }
}
