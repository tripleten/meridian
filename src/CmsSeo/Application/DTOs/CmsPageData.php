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

final class CmsPageData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $channel_id,
        public readonly string  $title,
        public readonly string  $url_key,
        public readonly ?string $content,
        public readonly string  $state,
        public readonly ?string $meta_title,
        public readonly ?string $meta_description,
        public readonly ?string $meta_keywords,
        public readonly bool    $meta_robots_noindex,
        public readonly ?string $published_at,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $page): self
    {
        return new self(
            id:                  $page->id,
            channel_id:          $page->channel_id,
            title:               $page->title,
            url_key:             $page->url_key,
            content:             $page->content,
            state:               $page->state,
            meta_title:          $page->meta_title,
            meta_description:    $page->meta_description,
            meta_keywords:       $page->meta_keywords,
            meta_robots_noindex: (bool) $page->meta_robots_noindex,
            published_at:        $page->published_at?->toIso8601String(),
            created_at:          $page->created_at->toIso8601String(),
        );
    }
}
