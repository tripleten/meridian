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

use Meridian\Customers\Domain\Repositories\CustomerRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class UpdateCustomerHandler
{
    public function __construct(
        private CustomerRepositoryInterface $customers,
    ) {}

    public function handle(UpdateCustomerCommand $command): void
    {
        $customer = $this->customers->findById($command->customerId);

        if ($customer === null) {
            throw new DomainException("Customer '{$command->customerId}' not found.");
        }

        $customer->customer_group_id           = $command->customer_group_id;
        $customer->first_name                  = $command->first_name;
        $customer->last_name                   = $command->last_name;
        $customer->phone                       = $command->phone;
        $customer->company                     = $command->company;
        $customer->gender                      = $command->gender;
        $customer->is_active                   = $command->is_active;
        $customer->is_subscribed_to_newsletter = $command->is_subscribed_to_newsletter;

        $this->customers->save($customer);
    }
}
