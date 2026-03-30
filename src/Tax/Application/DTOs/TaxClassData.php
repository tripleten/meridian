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

final class TaxClassData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:         $model->id,
            name:       $model->name,
            code:       $model->code,
            created_at: $model->created_at?->toIso8601String() ?? '',
        );
    }
}
