<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Application\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Meridian\Customers\Infrastructure\Persistence\EloquentCustomer;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class CreateCustomerHandler
{
    public function handle(CreateCustomerCommand $command): EloquentCustomer
    {
        if (EloquentCustomer::where('user_id', $command->user_id)->exists()) {
            throw new DomainException("A customer profile already exists for user ID {$command->user_id}.");
        }

        return DB::transaction(function () use ($command): EloquentCustomer {
            return EloquentCustomer::create([
                'id'                          => (string) Str::ulid(),
                'user_id'                     => $command->user_id,
                'customer_group_id'           => $command->customer_group_id,
                'first_name'                  => $command->first_name,
                'last_name'                   => $command->last_name,
                'phone'                       => $command->phone,
                'company'                     => $command->company,
                'gender'                      => $command->gender,
                'is_active'                   => true,
                'is_subscribed_to_newsletter' => $command->is_subscribed_to_newsletter,
            ]);
        });
    }
}
