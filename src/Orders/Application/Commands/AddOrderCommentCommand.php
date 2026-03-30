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

final readonly class AddOrderCommentCommand
{
    public function __construct(
        public string  $orderId,
        public string  $comment,
        public string  $author_type             = 'admin',
        public ?int    $author_id               = null,
        public bool    $is_customer_notified    = false,
        public bool    $is_visible_to_customer  = false,
    ) {}
}
