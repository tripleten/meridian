<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Application\Queries
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Application\Queries;

use Meridian\Customers\Infrastructure\Persistence\EloquentCustomer;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class GetCustomerHandler
{
    public function handle(GetCustomerQuery $query): EloquentCustomer
    {
        $customer = EloquentCustomer::with(['user', 'customerGroup', 'addresses'])
            ->find($query->customerId);

        if ($customer === null) {
            throw new DomainException("Customer '{$query->customerId}' not found.");
        }

        return $customer;
    }
}
