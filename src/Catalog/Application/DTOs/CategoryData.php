<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\DTOs;

use Spatie\LaravelData\Data;

final class CategoryData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $parent_id,
        public readonly string  $name,
        public readonly string  $url_key,
        public readonly ?string $description,
        public readonly bool    $is_active,
        public readonly int     $depth,
        public readonly int     $sort_order,
        public readonly ?string $meta_title,
        public readonly ?string $meta_description,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $category): self
    {
        return new self(
            id:               (string) $category->id,
            parent_id:        $category->parent_id ? (string) $category->parent_id : null,
            name:             $category->name,
            url_key:          $category->url_key,
            description:      $category->description,
            is_active:        (bool) $category->is_active,
            depth:            (int) ($category->depth ?? 0),
            sort_order:       (int) ($category->_lft ?? 0),
            meta_title:       $category->seo_title,
            meta_description: $category->seo_description,
            created_at:       $category->created_at->toDateTimeString(),
        );
    }
}
