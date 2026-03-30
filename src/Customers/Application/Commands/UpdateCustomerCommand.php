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

final readonly class UpdateCustomerCommand
{
    public function __construct(
        public string  $customerId,
        public ?string $customer_group_id,
        public string  $first_name,
        public string  $last_name,
        public ?string $phone,
        public ?string $company,
        public ?string $gender,
        public bool    $is_active,
        public bool    $is_subscribed_to_newsletter,
    ) {}
}
