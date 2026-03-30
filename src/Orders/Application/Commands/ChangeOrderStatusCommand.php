<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Application\Commands;

final readonly class ChangeOrderStatusCommand
{
    public function __construct(
        public string  $orderId,
        public string  $newStatus,
        public ?string $comment       = null,
        public bool    $notifyCustomer = false,
    ) {}
}
