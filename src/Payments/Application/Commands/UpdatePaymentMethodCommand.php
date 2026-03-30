<?php

declare(strict_types=1);

namespace Meridian\Payments\Application\Commands;

final class UpdatePaymentMethodCommand
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly bool    $is_active,
        public readonly int     $sort_order,
        public readonly array   $config,
    ) {}
}
