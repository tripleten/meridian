<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Application\DTOs;

use Spatie\LaravelData\Data;

class OrderCommentData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $author_type,
        public readonly string $comment,
        public readonly bool   $is_customer_notified,
        public readonly bool   $is_visible_to_customer,
        public readonly string $created_at,
    ) {}

    public static function fromModel(object $comment): self
    {
        return new self(
            id:                     $comment->id,
            author_type:            $comment->author_type,
            comment:                $comment->comment,
            is_customer_notified:   (bool) $comment->is_customer_notified,
            is_visible_to_customer: (bool) $comment->is_visible_to_customer,
            created_at:             $comment->created_at->toISOString(),
        );
    }
}
