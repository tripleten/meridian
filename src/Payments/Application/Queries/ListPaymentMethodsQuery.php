<?php

declare(strict_types=1);

namespace Meridian\Payments\Application\Queries;

final class ListPaymentMethodsQuery
{
    public function __construct(
        public readonly bool $activeOnly = false,
    ) {}
}
