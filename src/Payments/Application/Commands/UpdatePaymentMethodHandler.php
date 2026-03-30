<?php

declare(strict_types=1);

namespace Meridian\Payments\Application\Commands;

use Meridian\Payments\Infrastructure\Persistence\EloquentPaymentMethod;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class UpdatePaymentMethodHandler
{
    public function handle(UpdatePaymentMethodCommand $command): void
    {
        $method = EloquentPaymentMethod::find($command->id);

        if ($method === null) {
            throw new DomainException("Payment method [{$command->id}] not found.");
        }

        $method->name        = $command->name;
        $method->description = $command->description;
        $method->is_active   = $command->is_active;
        $method->sort_order  = $command->sort_order;
        $method->config      = $command->config;
        $method->save();
    }
}
