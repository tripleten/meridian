<?php

declare(strict_types=1);

namespace Meridian\Payments\Application\Queries;

use Illuminate\Support\Collection;
use Meridian\Payments\Infrastructure\Persistence\EloquentPaymentMethod;

final class ListPaymentMethodsHandler
{
    public function handle(ListPaymentMethodsQuery $query): Collection
    {
        $builder = EloquentPaymentMethod::orderBy('sort_order')->orderBy('name');

        if ($query->activeOnly) {
            $builder->where('is_active', true);
        }

        return $builder->get();
    }
}
