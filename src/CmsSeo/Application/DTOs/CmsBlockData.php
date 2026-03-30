<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Application\DTOs;

use Spatie\LaravelData\Data;

final class CmsBlockData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $channel_id,
        public readonly string  $identifier,
        public readonly string  $title,
        public readonly ?string $content,
        public readonly bool    $is_active,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $block): self
    {
        return new self(
            id:         $block->id,
            channel_id: $block->channel_id,
            identifier: $block->identifier,
            title:      $block->title,
            content:    $block->content,
            is_active:  (bool) $block->is_active,
            created_at: $block->created_at->toIso8601String(),
        );
    }
}
